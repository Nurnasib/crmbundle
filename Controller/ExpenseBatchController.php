<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Admin\Location;
use App\Entity\User;
use DoctrineExtensions\Query\Mysql\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\DmsFile;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\ExpenseBatch;
use Terminalbd\CrmBundle\Entity\ExpenseChart;
use Terminalbd\CrmBundle\Entity\ExpenseConveyanceDetails;
use Terminalbd\CrmBundle\Entity\ExpenseParticular;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ExpenseFormType;
use Terminalbd\CrmBundle\Form\SettingFormType;

/**
 * @Route("/crm/expense-batch")
 * @Security("is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class ExpenseBatchController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_expense_batch")
     * @return Response
     */
    public function index(): Response
    {
        $entities = $this->getDoctrine()->getRepository(ExpenseBatch::class)->getExpenseBatches($this->getUser());
        return $this->render('@TerminalbdCrm/expenseBatch/index.html.twig',[
            'entities' => $entities
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/details", methods={"GET", "POST"}, name="crm_expense_batch_details")
     * @param Request $request
     * @param User $employee
     * @return Response
     */
    public function details(Request $request, ExpenseBatch $entity): Response
    {
        $monthlyExpenseParticularAttributes = $this->getDoctrine()->getRepository(Setting::class)->getMonthlyExpenseParticular();
        $expensePaticularAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getDailyExpenseParticularAmount($entity->getEmployee(),null, $entity);
        $conveyanceDetailsTotalAmount = $this->getDoctrine()->getRepository(ExpenseConveyanceDetails::class)->getTotalAmountConveyanceDetailsByExpense($entity->getEmployee(), null, $entity);


        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($entity->getEmployee()?$entity->getEmployee()->getId():null);
        $fixedDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='FIXED') return $n; }, $expenseChartByEmployee));

        $areaWiseExpenseParticular = [];
        if($fixedDailyExpenseParticular && sizeof($fixedDailyExpenseParticular)>0){
            foreach ($fixedDailyExpenseParticular as $expenseChart) {
                $areaWiseExpenseParticular['areaName'][$expenseChart['areaId']]=$expenseChart['areaName'];
                $areaWiseExpenseParticular['chartDetails'][$expenseChart['areaId']][$expenseChart['expenseChartDetailId']]=$expenseChart;
            }
        }


        $typeOfVehicles = $this->typeOfVehicle($entity->getEmployee()?$entity->getEmployee():null);

//dd($conveyanceDetailsTotalAmount);
        return $this->render('@TerminalbdCrm/expenseBatch/details.html.twig', [
            'expenseBatch' => $entity,
            'dailyExpenseParticularAttributes' => isset($expensePaticularAmount['expenseParticularAttributes']) && sizeof($expensePaticularAmount['expenseParticularAttributes'])>0?$expensePaticularAmount['expenseParticularAttributes']:[],
            'monthlyExpenseParticularAttributes' => $monthlyExpenseParticularAttributes,
            'expensePaticularAmount' => $expensePaticularAmount,
            'areaWiseExpenseParticulars' => $areaWiseExpenseParticular,
            'conveyanceDetailsTotalAmount' => $conveyanceDetailsTotalAmount,
            'typeOfVehicles' => $typeOfVehicles,
        ]);
    }

    private function typeOfVehicle(User $employee){
        $type_of_vehicle = [];
        if ($employee->getExpenseChart() && $employee->getExpenseChart()->getTypeOfVehicle() && $employee->getExpenseChart()->getTypeOfVehicle() == 'car') {
            $type_of_vehicle =
                [
                    [
                        'id' => 1,
                        'slug' => 'office-car',
                        'name' => 'Office Car',
                    ],
                    [
                        'id' => 2,
                        'slug' => 'car',
                        'name' => 'Car',
                    ],
                    [
                        'id' => 3,
                        'slug' => 'local-conveyance',
                        'name' => 'Local Conveyance',
                    ],
                    [
                        'id' => 4,
                        'slug' => 'others',
                        'name' => 'others',
                    ]
                ];
        } elseif ($employee->getExpenseChart() && $employee->getExpenseChart()->getTypeOfVehicle() && $employee->getExpenseChart()->getTypeOfVehicle() == 'motorcycle') {
            $type_of_vehicle =
                [
                    [
                        'id' => 1,
                        'slug' => 'office-car',
                        'name' => 'Office Car',
                    ],
                    [
                        'id' => 2,
                        'slug' => 'motorcycle',
                        'name' => 'Motorcycle',
                    ],
                    [
                        'id' => 3,
                        'slug' => 'local-conveyance',
                        'name' => 'Local Conveyance',
                    ],
                    [
                        'id' => 4,
                        'slug' => 'others',
                        'name' => 'others',
                    ]
                ];
        } else {
            $type_of_vehicle =
                [
                    [
                        'id' => 1,
                        'slug' => 'office-car',
                        'name' => 'Office Car',
                    ],
                    [
                        'id' => 2,
                        'slug' => 'local-conveyance',
                        'name' => 'Local Conveyance',
                    ],
                    [
                        'id' => 3,
                        'slug' => 'others',
                        'name' => 'others',
                    ]
                ];
        }

        return $type_of_vehicle;
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/approved", methods={"GET", "POST"}, name="crm_expense_batch_approved")
     * @param Request $request
     * @param User $employee
     * @return Response
     */
    public function approved(Request $request, ExpenseBatch $entity): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setStatus(2);
        $entity->setApprovedBy($this->getUser());
        $entity->setApprovedAt(new \DateTime('now'));
        if($entity->getExpenses()){
            /* @var Expense $expense*/
            foreach ($entity->getExpenses() as $expense) {
                $expense->setStatus(3);
                $em->persist($expense);
                $em->flush();
            }
        }

        $em->persist($entity);
        $em->flush();

        $this->addFlash('success', 'Expense has been approved.');
        return $this->redirectToRoute('crm_expense_batch');
    }

}
