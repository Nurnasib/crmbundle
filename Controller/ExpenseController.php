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
use Terminalbd\CrmBundle\Entity\CrmConfig;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\DmsFile;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\ExpenseBatch;
use Terminalbd\CrmBundle\Entity\ExpenseChart;
use Terminalbd\CrmBundle\Entity\ExpenseParticular;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ExpenseFormType;
use Terminalbd\CrmBundle\Form\ExpenseVehicleFormType;
use Terminalbd\CrmBundle\Form\SettingFormType;

/**
 * @Route("/crm/expense")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_DEVELOPER')")
 */
class ExpenseController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_expense")
     * @return Response
     */
    public function index(): Response
    {
        $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpenses($this->getUser());
        $dailyExpenseParticularAttributes = $this->getDoctrine()->getRepository(Setting::class)->getDailyExpenseParticular();
        $expensePaticularTotalAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getTotalAmountExpenseParticular($this->getUser());

        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($this->getUser()?$this->getUser()->getId():null);
        
        
        return $this->render('@TerminalbdCrm/expense/index.html.twig',[
            'entities' => $entities,
            'expensePaticularTotalAmount' => $expensePaticularTotalAmount,
            'expenseParticularAttributes' => $dailyExpenseParticularAttributes,
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_expense_new")
     * @return Response
     */
    public function new(Request $request): Response
    {

        $entity = new Expense();

        /*$exitingExpense = $this->getDoctrine()->getRepository(Expense::class)->findOneBy(array('crmVisit'=>$crmVisit));
        if($exitingExpense){
            return $this->redirectToRoute('crm_expense_edit', ['id'=>$exitingExpense->getId()]);
        }

        $entity->setVisitingArea($location);
        $entity->setCrmVisit($crmVisit);*/
        $entity->setEmployee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
//        $this->addFlash('success', 'post.created_successfully');
        return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_expense_edit")
     * @param Request $request
     * @param Expense $entity
     * @return Response
     */

    public function edit(Request $request, Expense $entity): Response
    {
        $form = $this->createForm(ExpenseFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData=$form->getData();
            $expenseDate=$formData->getExpenseDate()->format('Y-m-d');

            $employee= $this->getUser();
            $yearMonth = $formData->getExpenseDate()->format('Y-m');
            $expenseMonth = date('Y-m-d',strtotime($yearMonth.'-01'));

            $existingExpenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);

            if($existingExpenseBatch){
                $this->addFlash('error', $yearMonth.' month expense already process.');
                return $this->redirectToRoute('crm_expense_details', ['employee'=>$employee->getId(),'monthYear'=>$yearMonth]);
            }

            $existingExpenseCheck=$this->getDoctrine()->getRepository(Expense::class)->getExpenseByEmployeeAndDate($entity, $this->getUser(),$expenseDate);
            if($existingExpenseCheck && sizeof($existingExpenseCheck)>0) {

                $this->addFlash('error', $expenseDate.' date expense already exist.');
                return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
            }



            $data = $request->request->all();
//            dd($data);
            $em = $this->getDoctrine()->getManager();

            $entity->setStatus(1);

            $em->persist($entity);

            if(isset($data['particular_id']) && sizeof($data['particular_id'])>0){
                foreach ($data['particular_id'] as $expenseId=>$expenseDetailParticulars) {
                    foreach ($expenseDetailParticulars as $expenseParticularId => $particulars) {
                        foreach ($particulars as $particularId=>$particular) {
                            $particularObj=$this->getDoctrine()->getRepository(Setting::class)->find($particularId);
                            $requestAmount = isset($data['amount'][$expenseId][$expenseParticularId]) ? $data['amount'][$expenseId][$expenseParticularId][$particularId]: '';
                            $amount = $requestAmount && $requestAmount!=''?$requestAmount:null;

                            $existingExpenseParticular=$this->getDoctrine()->getRepository(ExpenseParticular::class)->findOneBy(['expense'=>$entity, 'particular'=>$particularObj, 'expenseChartDetailId'=>$expenseParticularId]);

                            if($existingExpenseParticular){
                                $expenseParticular=$existingExpenseParticular;
                            }else{
                                $expenseParticular= new ExpenseParticular();
                            }

                            $expenseParticular->setAmount($amount);
                            $expenseParticular->setExpense($entity);
                            $expenseParticular->setExpenseChartDetailId($expenseParticularId);
                            $expenseParticular->setParticular($particularObj?$particularObj:null);

                            if( isset($_FILES['files']) && $_FILES['files']['size'][$expenseId][$particularId] != 0 && $_FILES['files']['error'][$expenseId][$particularId] == 0){

                                $files = empty($_FILES['files']) ? '' : $_FILES['files'];

                                $fileName = $entity->getId() . '-' .$particularId . '-' . time() . "-" . $files['name'][$expenseId][$particularId];

                                $file_tmp = $files['tmp_name'][$expenseId][$particularId];
                                if (is_dir('uploads/expense/') == false) {
                                    mkdir('uploads/expense/', 0777);        // Create directory if it does not exist
                                }
                                if (is_dir('uploads/expense/' . $fileName) == false) {
                                    move_uploaded_file($file_tmp, 'uploads/expense/' . $fileName);

                                    $expenseParticular->setPath($fileName);
                                }
                            }

                            $em->persist($expenseParticular);
                        }
                    }

                }
            }

            $em->flush();
            $this->addFlash('success', 'Expense has been updated successfully');
            return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
        }
        $dailyExpenseParticulars = $this->getDoctrine()->getRepository(Setting::class)->getDailyExpenseParticular();

        $expenseParticularByExpense = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getExpenseParticularsByExpense($entity);
        $attchments = $this->getDoctrine()->getRepository(DmsFile::class)->getDmsAttchmentFile($entity,'CRM','Expense');
//dd($expenseParticularByExpense);

        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($this->getUser()?$this->getUser()->getId():null);
        $fixedDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='FIXED') return $n; }, $expenseChartByEmployee));
        $userDefineDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='USER_DEFINE') return $n; }, $expenseChartByEmployee));
        return $this->render('@TerminalbdCrm/expense/new.html.twig', [
            'entity' => $entity,
            'dailyExpenseParticulars' => $dailyExpenseParticulars,
            'attchments' => $attchments,
            'expenseParticulars' => $expenseParticularByExpense,
            'form' => $form->createView(),
            'userDesignationId' => $this->getUser()->getDesignation()?$this->getUser()->getDesignation()->getId():null,
            'expenseChart' => $expenseChartByEmployee,
            'fixedDailyExpenseParticular' => $fixedDailyExpenseParticular,
            'userDefineDailyExpenseParticular' => $userDefineDailyExpenseParticular,
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{employee}/details", methods={"GET", "POST"}, name="crm_expense_details")
     * @param Request $request
     * @param User $employee
     * @return Response
     */
    public function details(Request $request, User $employee): Response
    {
        $data=$request->query->all();
        
        $yearMonth = isset($data['monthYear'])&&$data['monthYear']!=''?$data['monthYear']:date('Y-m');

        $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpensesByEmployeeAndYearMonth($employee , $yearMonth);
        if(sizeof($entities)==0) {
            return $this->redirectToRoute('crm_expense');
        }
        $expenseMonth = date('Y-m-d',strtotime($yearMonth.'-01'));
        $expenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);
        $monthlyExpenseParticularAttributes = $this->getDoctrine()->getRepository(Setting::class)->getMonthlyExpenseParticular();

        $expensePaticularAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getDailyExpenseParticularAmount($this->getUser(),$yearMonth);

        $monthlyExpensePaticularAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getMonthlyExpenseParticularAmount($this->getUser(),$yearMonth);
        $crmConfig= $this->getDoctrine()->getRepository(CrmConfig::class)->findOneBy(['slug'=>'bike-miles-per-km','status'=>1]);
//dd($monthlyExpensePaticularAmount);
        return $this->render('@TerminalbdCrm/expense/details.html.twig', [
            'entities' => $entities,
            'employee' => $employee,
            'yearMonth' => $yearMonth,
            'expenseBatch' => $expenseBatch,
            'expenseParticularAttributes' => isset($expensePaticularAmount['expenseParticularAttributes']) && sizeof($expensePaticularAmount['expenseParticularAttributes'])>0?$expensePaticularAmount['expenseParticularAttributes']:[],
            'monthlyExpenseParticularAttributes' => $monthlyExpenseParticularAttributes,
            'expensePaticularAmount' => $expensePaticularAmount,
            'monthlyExpensePaticularAmount' => $monthlyExpensePaticularAmount,
            'crmConfig' => $crmConfig,
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{employee}/process", methods={"GET", "POST"}, name="crm_expense_process")
     * @param Request $request
     * @param User $employee
     * @return Response
     */
    public function process(Request $request, User $employee): Response
    {
        $requestData=$request->request->all();
        $yearMonth = isset($requestData['monthYear'])&&$requestData['monthYear']!=''?$requestData['monthYear']:date('Y-m');
        $expenseMonth = date('Y-m-d',strtotime($yearMonth.'-01'));

        $existingExpenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);

        if($existingExpenseBatch){
            $this->addFlash('error', $yearMonth.' month expense already process.');
            return $this->redirectToRoute('crm_expense_details', ['employee'=>$employee->getId(),'monthYear'=>$yearMonth]);
        }
        $crmConfig= $this->getDoctrine()->getRepository(CrmConfig::class)->findOneBy(['slug'=>'bike-miles-per-km','status'=>1]);
        if ($requestData && isset($requestData['expense']) && sizeof($requestData['expense'])>0) {
            $expenseBatch = new ExpenseBatch();

            $expenseBatch->setEmployee($employee);
            $expenseBatch->setStatus(1);
            $expenseBatch->setExpenseMonth(new \DateTime($expenseMonth));
            $totalReading = isset($requestData['totalRiding']) && $requestData['totalRiding'] != '' && $requestData['totalRiding'] > 0 ? $requestData['totalRiding'] : '0';
            $expenseBatch->setTotalRiding($totalReading);

            $perMilesAmount = $crmConfig && $crmConfig->getValue() && $crmConfig->getValue() > 0 ? $crmConfig->getValue() : 0;

            $expenseBatch->setPerMilesAmount($perMilesAmount);

            $expenseBatch->setTotalMilesAmount($totalReading * $perMilesAmount);


            $em = $this->getDoctrine()->getManager();
            $em->persist($expenseBatch);
            $em->flush();

            if (isset($requestData['particular_id']) && sizeof($requestData['particular_id']) > 0) {
                foreach ($requestData['particular_id'] as $particularId => $particular) {
                    $particularObj = $this->getDoctrine()->getRepository(Setting::class)->find($particularId);
                    $requestAmount = $requestData['amount'][$particularId];
                    $amount = $requestAmount && $requestAmount != '' ? $requestAmount : null;

                    $existingExpenseParticular = $this->getDoctrine()->getRepository(ExpenseParticular::class)->findOneBy(['expenseBatch' => $expenseBatch, 'particular' => $particularObj]);

                    if ($existingExpenseParticular) {
                        $expenseParticular = $existingExpenseParticular;
                    } else {
                        $expenseParticular = new ExpenseParticular();
                    }

                    $expenseParticular->setAmount($amount);
                    $expenseParticular->setExpenseBatch($expenseBatch);
                    $expenseParticular->setParticular($particularObj ? $particularObj : null);

                    if ($_FILES['files']['size'][$particularId] != 0 && $_FILES['files']['error'][$particularId] == 0) {

                        $files = empty($_FILES['files']) ? '' : $_FILES['files'];

                        $fileName = $expenseBatch->getId() . '-' . $particularId . '-' . time() . "-" . $files['name'][$particularId];

                        $file_tmp = $files['tmp_name'][$particularId];
                        if (is_dir('uploads/expense/') == false) {
                            mkdir('uploads/expense/', 0777);        // Create directory if it does not exist
                        }
                        if (is_dir('uploads/expense/' . $fileName) == false) {
                            move_uploaded_file($file_tmp, 'uploads/expense/' . $fileName);

                            $expenseParticular->setPath($fileName);
                        }
                    }

                    $em->persist($expenseParticular);

                }
            }


            if ($requestData && isset($requestData['expense']) && sizeof($requestData['expense']) > 0) {
                foreach ($requestData['expense'] as $expenseId => $expense) {
                    /* @var Expense $expenseObj */
                    $expenseObj = $this->getDoctrine()->getRepository(Expense::class)->find($expense);

                    $expenseObj->setExpenseBatch($expenseBatch);
                    $expenseObj->setStatus(2);

                    $em->persist($expenseObj);
                    $em->flush();
                }

            }
            $this->addFlash('success', 'Expense has been process successfully');

            return $this->redirectToRoute('crm_expense_details', ['employee' => $employee->getId(), 'monthYear' => $yearMonth]);
        }else{
            $this->addFlash('error', 'Oop! Something wrong.');
            return $this->redirectToRoute('crm_expense_details', ['employee' => $employee->getId(), 'monthYear' => $yearMonth]);
        }

    }

    /**
     * Deletes a Expense entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_expense_delete")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(Expense::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'Expense has been deleted successfully.');
        return new Response('Success');
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/attachment/{id}/delete", methods={"GET"}, name="crm_expense_attachment_delete", options={"expose"=true})
     */
    public function attachmentDelete(DmsFile $entity): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return new JsonResponse(['status'=>200, 'message'=>'Success']);
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/particular/type", methods={"GET"}, name="crm_expense_particular_type")
     * @param Request $request
     * @param User $employee
     * @return Response
     */
    public function getExpenseParticular(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>['DAILY_EXPENSE_PARTICULAR', 'MONTHLY_EXPENSE_PARTICULAR']),array('settingType'=>'asc'));
        return $this->render('@TerminalbdCrm/expense/expense-vehicle.html.twig',[
            'entities' => $entities
        ]);
    }

    /**
     * @Route("/particular/type/new", methods={"GET", "POST"}, name="crm_expense_vehicle_new")
     * @param Request $request
     * @return Response
     */
    public function createExpenseParticular(Request $request): Response
    {

        $entity = new Setting();

        $form = $this->createForm(ExpenseVehicleFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_expense_vehicle_new');
            }
            return $this->redirectToRoute('crm_expense_particular_type');
        }
        return $this->render('@TerminalbdCrm/expense/expense-vehicle-new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/particular/type/{id}/edit", methods={"GET", "POST"}, name="crm_expense_vehicle_edit")
     * @param Request $request
     * @param Setting $entity
     * @return Response
     */

    public function editExpenseParticular(Request $request, Setting $entity): Response
    {
        $form = $this->createForm(ExpenseVehicleFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            //$this->getDoctrine()->getRepository(ItemKeyValue::class)->insertSettingKeyValue($entity,$data);
            /*if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_setting', ['id' => $entity->getId()]);
            }*/
            return $this->redirectToRoute('crm_expense_particular_type');
        }
        return $this->render('@TerminalbdCrm/expense/expense-vehicle-new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }
}
