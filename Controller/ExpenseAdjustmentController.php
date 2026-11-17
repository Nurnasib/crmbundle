<?php

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\ExpenseAdjustmentLog;
use Terminalbd\CrmBundle\Entity\ExpenseConveyanceDetails;
use Terminalbd\CrmBundle\Form\CrmTourPlanSearchFormType;

/**
 * Manual adjustment of an employee's monthly expenses: edit motorcycle conveyance bills
 * (never mileage or meter readings) and delete the employee's latest expense.
 * Every change is written to crm_expense_adjustment_log in the same flush.
 *
 * Access mirrors the Expense For Approval screens (ExpenseBatchController).
 *
 * @Route("/crm/expense-adjustment")
 * @Security("is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class ExpenseAdjustmentController extends AbstractController
{
    /** Motorcycle row fields this page may change. Mileage and meter readings are deliberately absent. */
    const EDITABLE_FIELDS = [
        'Amount' => 'Amount',
        'FuelBill' => 'Fuel Bill',
        'TollBill' => 'Toll Bill',
        'ParkingBill' => 'Parking Bill',
        'OthersBill' => 'Others Bill',
        'MobilBill' => 'Mobil Bill',
        'MaintenanceBill' => 'Maintenance Bill',
        'ServicingBill' => 'Servicing Bill',
        'PersonalBikeAllowance' => 'Personal Bike Allowance',
    ];

    /** Meter columns; editable, but mileage (total_mileage) is not, so the running km counters stay valid. */
    const METER_FIELDS = [
        'MeterReadingFrom' => 'Meter Start',
        'MeterReadingTo' => 'Meter End',
    ];

    /**
     * @Route("/", methods={"GET"}, name="crm_expense_adjustment")
     */
    public function index(Request $request): Response
    {
        $form = $this->searchForm();
        $form->handleRequest($request);

        $employee = null;
        $requestDate = date('Y-m');
        $expenses = [];
        $latestExpenseId = null;
        $logs = [];

        if ($form->isSubmitted()) {
            $filterBy = $form->getData();
            $employee = $filterBy['employee'] ?? null;
            $requestDate = isset($filterBy['visitDate']) ? $filterBy['visitDate']->format('Y-m') : date('Y-m');

            if ($employee) {
                // Newest first.
                $expenses = array_reverse($this->getDoctrine()->getRepository(Expense::class)->getExpensesByEmployeeAndYearMonth($employee, $requestDate));
                $latest = $this->latestExpense($employee);
                $latestExpenseId = $latest ? $latest->getId() : null;

                $logs = $this->getDoctrine()->getRepository(ExpenseAdjustmentLog::class)->createQueryBuilder('l')
                    ->where('l.employee = :employee')->setParameter('employee', $employee)
                    ->andWhere("DATE_FORMAT(l.expenseDate,'%Y-%m') = :month")->setParameter('month', $requestDate)
                    ->orderBy('l.changedAt', 'DESC')
                    ->getQuery()->getResult();
            }
        }

        return $this->render('@TerminalbdCrm/expenseAdjustment/index.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
            'expenses' => $expenses,
            'latestExpenseId' => $latestExpenseId,
            'logs' => $logs,
            'requestDate' => $requestDate,
            'editableFields' => self::EDITABLE_FIELDS,
        ]);
    }

    /**
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_expense_adjustment_edit")
     */
    public function edit(Request $request, Expense $expense): Response
    {
        $this->denyUnlessManaged($expense);

        $rows = array_values(array_filter($expense->getExpenseConveyanceDetails()->toArray(), function (ExpenseConveyanceDetails $d) {
            return $d->getTransportType() === 'motorcycle';
        }));

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('expense_adjust_' . $expense->getId(), $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }
            $reason = trim((string) $request->request->get('reason'));
            if ($reason === '') {
                $this->addFlash('error', 'A reason is required.');
                return $this->redirectToRoute('crm_expense_adjustment_edit', ['id' => $expense->getId()]);
            }

            $posted = $request->request->all();
            $fields = $posted['fields'] ?? [];
            $em = $this->getDoctrine()->getManager();
            $changedRows = 0;

            foreach ($rows as $row) {
                $id = $row->getId();
                $old = [];
                $new = [];

                $numericFields = self::EDITABLE_FIELDS + self::METER_FIELDS;
                foreach (array_keys($numericFields) as $field) {
                    if (!isset($fields[$id][$field])) {
                        continue;
                    }
                    $before = (float) $row->{'get' . $field}();
                    $after = (float) $fields[$id][$field];
                    if ($after < 0) {
                        $this->addFlash('error', $numericFields[$field] . ' cannot be negative.');
                        return $this->redirectToRoute('crm_expense_adjustment_edit', ['id' => $expense->getId()]);
                    }
                    if ($before !== $after) {
                        $old[$field] = $before;
                        $new[$field] = $after;
                        $row->{'set' . $field}($after);
                    }
                }

                $details = isset($fields[$id]['Details']) ? trim($fields[$id]['Details']) : null;
                if ($details !== null && $details !== (string) $row->getDetails()) {
                    $old['Details'] = $row->getDetails();
                    $new['Details'] = $details === '' ? null : $details;
                    $row->setDetails($new['Details']);
                }

                $destination = $row->getDestination();
                if (is_array($destination) && $destination) {
                    $updated = $destination;
                    // Only the meter readings in the JSON change; keep the JSON meter readings equal to the columns (first segment start, last segment end).
                    $lastIndex = array_key_last($updated);
                    $firstIndex = array_key_first($updated);
                    if (isset($updated[$firstIndex]['meterReadingStart']) && (float) $updated[$firstIndex]['meterReadingStart'] !== (float) $row->getMeterReadingFrom()) {
                        $updated[$firstIndex]['meterReadingStart'] = (string) $row->getMeterReadingFrom();
                    }
                    if (isset($updated[$lastIndex]['meterReadingEnd']) && (float) $updated[$lastIndex]['meterReadingEnd'] !== (float) $row->getMeterReadingTo()) {
                        $updated[$lastIndex]['meterReadingEnd'] = (string) $row->getMeterReadingTo();
                    }
                    if ($updated !== $destination) {
                        $old['destination'] = $destination;
                        $new['destination'] = $updated;
                        $row->setDestination($updated);
                    }
                }

                if (!$new) {
                    continue;
                }

                $oldTotal = (float) $row->getTotalAmount();
                $row->setTotalAmount($row->calculateTotalAmount());
                if ($oldTotal !== (float) $row->getTotalAmount()) {
                    $old['TotalAmount'] = $oldTotal;
                    $new['TotalAmount'] = (float) $row->getTotalAmount();
                }

                $log = $this->newLog($expense, ExpenseAdjustmentLog::ACTION_EDIT, $reason);
                $log->setConveyanceDetailId($id);
                $log->setOldData($old);
                $log->setNewData($new);
                $em->persist($log);
                $changedRows++;
            }

            if ($changedRows === 0) {
                $this->addFlash('error', 'Nothing was changed.');
                return $this->redirectToRoute('crm_expense_adjustment_edit', ['id' => $expense->getId()]);
            }

            // One flush = one transaction: the change and its log row are saved together or not at all.
            $em->flush();
            $this->addFlash('success', 'Expense has been adjusted.');

            return $this->redirectToIndex($expense);
        }

        return $this->render('@TerminalbdCrm/expenseAdjustment/edit.html.twig', [
            'expense' => $expense,
            'rows' => $rows,
            'editableFields' => self::EDITABLE_FIELDS,
            'meterFields' => self::METER_FIELDS,
        ]);
    }

    /**
     * Only the employee's latest expense can be deleted, so the running mileage counters of later rows never break.
     *
     * @Route("/{id}/delete", methods={"POST"}, name="crm_expense_adjustment_delete")
     */
    public function delete(Request $request, Expense $expense): Response
    {
        $this->denyUnlessManaged($expense);

        if (!$this->isCsrfTokenValid('expense_adjust_delete_' . $expense->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
        $reason = trim((string) $request->request->get('reason'));
        if ($reason === '') {
            $this->addFlash('error', 'A reason is required to delete an expense.');
            return $this->redirectToIndex($expense);
        }
        $latest = $this->latestExpense($expense->getEmployee());
        if (!$latest || $latest->getId() !== $expense->getId()) {
            $this->addFlash('error', 'Only the employee\'s latest expense can be deleted.');
            return $this->redirectToIndex($expense);
        }

        // Raw rows, so the snapshot holds every column of the expense and of each child table the delete removes.
        $conn = $this->getDoctrine()->getConnection();
        $id = $expense->getId();
        $snapshot = [
            'expense' => $conn->fetchAssociative('SELECT * FROM crm_expense WHERE id = ?', [$id]),
            'conveyance_details' => $conn->fetchAllAssociative('SELECT * FROM crm_expense_conveyance_details WHERE expense_id = ?', [$id]),
            'particulars' => $conn->fetchAllAssociative('SELECT * FROM crm_expense_particulars WHERE expense_id = ?', [$id]),
            'purposes' => $conn->fetchAllAssociative('SELECT * FROM crm_expence_purpose WHERE expense_id = ?', [$id]),
            'vehicles' => $conn->fetchAllAssociative('SELECT * FROM crm_expence_vehicle WHERE expense_id = ?', [$id]),
        ];

        $redirect = $this->redirectToIndex($expense);

        $em = $this->getDoctrine()->getManager();
        $log = $this->newLog($expense, ExpenseAdjustmentLog::ACTION_DELETE, $reason);
        $log->setOldData($snapshot);
        $em->persist($log);
        $em->remove($expense);
        // One flush = one transaction: the delete and its snapshot are saved together or not at all.
        $em->flush();

        $this->addFlash('success', 'Expense has been deleted. A full copy is kept in the adjustment history.');

        return $redirect;
    }

    private function searchForm(): FormInterface
    {
        return $this->createForm(CrmTourPlanSearchFormType::class, null, [
            'loggedUser' => $this->getUser(),
            'userRepo' => $this->getDoctrine()->getRepository(User::class),
            'method' => 'GET',
        ]);
    }

    /** Same employee scope as the search form's employee list: a manager cannot reach another team's expense by URL. */
    private function denyUnlessManaged(Expense $expense): void
    {
        $employee = $expense->getEmployee();
        if ($employee) {
            $form = $this->searchForm();
            $form->submit(['employee' => (string) $employee->getId()], false);
            $picked = $form->get('employee')->getData();
            if ($picked && $picked->getId() === $employee->getId()) {
                return;
            }
        }
        throw $this->createAccessDeniedException('You cannot adjust this employee\'s expenses.');
    }

    private function latestExpense(User $employee): ?Expense
    {
        return $this->getDoctrine()->getRepository(Expense::class)->createQueryBuilder('e')
            ->where('e.employee = :employee')->setParameter('employee', $employee)
            ->andWhere('e.status >= 1')
            ->andWhere('e.expenseDate IS NOT NULL')
            ->orderBy('e.expenseDate', 'DESC')->addOrderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    private function newLog(Expense $expense, string $action, string $reason): ExpenseAdjustmentLog
    {
        $log = new ExpenseAdjustmentLog();
        $log->setExpenseId($expense->getId());
        $log->setEmployee($expense->getEmployee());
        $log->setExpenseDate($expense->getExpenseDate());
        $log->setAction($action);
        $log->setReason($reason);
        $log->setChangedBy($this->getUser());

        return $log;
    }

    private function redirectToIndex(Expense $expense): Response
    {
        return $this->redirectToRoute('crm_expense_adjustment', [
            'crm_tour_plan_search_form' => [
                'employee' => $expense->getEmployee() ? $expense->getEmployee()->getId() : '',
                'visitDate' => $expense->getExpenseDate() ? $expense->getExpenseDate()->format('m-Y') : '',
            ],
        ]);
    }
}
