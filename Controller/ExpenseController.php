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
use App\Entity\Core\Company;
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
use Terminalbd\CrmBundle\Entity\ExpenseConveyanceDetails;
use Terminalbd\CrmBundle\Entity\ExpenseParticular;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmTourPlanSearchFormType;
use Terminalbd\CrmBundle\Form\ExpenseFormType;
use Terminalbd\CrmBundle\Form\ExpenseReportSearchFormType;
use Terminalbd\CrmBundle\Form\ExpenseVehicleFormType;
use Terminalbd\CrmBundle\Form\SettingFormType;

/**
 * @Route("/crm/expense")
 * @Security("is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_DEVELOPER') or is_granted('ROLE_VIEW_ONLY')")
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

        $conveyenceTotalAmount = $this->getDoctrine()->getRepository(ExpenseConveyanceDetails::class)->getTotalAmountMonthlyByEmployeeYear($this->getUser());

        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($this->getUser()?$this->getUser()->getId():null);
        $fixedDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='FIXED') return $n; }, $expenseChartByEmployee));

        $areaWiseExpenseParticular = [];
        if($fixedDailyExpenseParticular && sizeof($fixedDailyExpenseParticular)>0){
            foreach ($fixedDailyExpenseParticular as $expenseChart) {
                $areaWiseExpenseParticular['areaName'][$expenseChart['areaId']]=$expenseChart['areaName'];
                $areaWiseExpenseParticular['chartDetails'][$expenseChart['areaId']][$expenseChart['expenseChartDetailId']]=$expenseChart;
            }
        }
        $typeOfVehicles = $this->typeOfVehicle($this->getUser());

        return $this->render('@TerminalbdCrm/expense/index.html.twig',[
            'entities' => $entities,
            'expensePaticularTotalAmount' => $expensePaticularTotalAmount,
            'expenseParticularAttributes' => $dailyExpenseParticularAttributes,
            'areaWiseExpenseParticulars' => $areaWiseExpenseParticular,
            'conveyenceTotalAmount' => $conveyenceTotalAmount,
            'typeOfVehicles' => $typeOfVehicles,
            'employeeVehicleType' => $this->getUser()->getExpenseChart()?$this->getUser()->getExpenseChart()->getTypeOfVehicle():'',
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_expense_new")
     * @return Response
     */
    public function new(Request $request): Response
    {

//        $entity = new Expense();
//        $entity->setEmployee($this->getUser());
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($entity);
//        $em->flush();
//        $this->addFlash('success', 'post.created_successfully');
//        return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
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
//        dd($this->getUser()->getRoles());
        $employee = $entity && $entity->getEmployee()?$entity->getEmployee():$this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $formData=$form->getData();
            $expenseDate=$formData->getExpenseDate()->format('Y-m-d');

            $yearMonth = $formData->getExpenseDate()->format('Y-m');
            $expenseMonth = date('Y-m-d',strtotime($yearMonth.'-01'));

            $existingExpenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);
            $rolesString = implode('_', $this->getUser()->getRoles());

            if($existingExpenseBatch && !str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $this->getUser()->getRoles()) ){

                $this->addFlash('error', $yearMonth.' month expense already process.');
                return $this->redirectToRoute('crm_expense_details', ['employee'=>$employee->getId(),'monthYear'=>$yearMonth]);
            } elseif($existingExpenseBatch && (str_contains($rolesString, 'ADMIN') || in_array('ROLE_LINE_MANAGER', $this->getUser()->getRoles())) && $entity->getEmployee()->getId()==$this->getUser()->getId()){
                $this->addFlash('error', $yearMonth.' month expense already process.');
                return $this->redirectToRoute('crm_expense_details', ['employee'=>$employee->getId(),'monthYear'=>$yearMonth]);
            }


            $existingExpenseCheck=$this->getDoctrine()->getRepository(Expense::class)->duplicateExpenseCheckByEmployeeAndDate($entity, $employee, $expenseDate);
            if($existingExpenseCheck && sizeof($existingExpenseCheck)>0) {

                $this->addFlash('error', $expenseDate.' date expense already exist.');
                return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
            }



            $data = $request->request->all();
//            dd($data);
            $em = $this->getDoctrine()->getManager();

            $entity->setStatus($entity && $entity->getStatus()?$entity->getStatus():1);

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

        $expenseConveyanceDetails = $this->getDoctrine()->getRepository(ExpenseConveyanceDetails::class)->getConveyanceDetailsByExpense($entity);
//dd($expenseConveyanceDetails);
        $attchments = $this->getDoctrine()->getRepository(DmsFile::class)->getDmsAttchmentFile($entity,'CRM','Expense');

        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($entity && $entity->getEmployee()?$entity->getEmployee()->getId():$this->getUser()->getId());
        $fixedDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='FIXED') return $n; }, $expenseChartByEmployee));
        $userDefineDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='USER_DEFINE') return $n; }, $expenseChartByEmployee));

        $typeOfVehicles = $this->typeOfVehicle( $employee );

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
            'expenseConveyanceDetails' => $expenseConveyanceDetails,
            'typeOfVehicles' => $typeOfVehicles,
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

        $expensePaticularAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getDailyExpenseParticularAmount($employee, $yearMonth);

        $conveyanceDetailsTotalAmount = $this->getDoctrine()->getRepository(ExpenseConveyanceDetails::class)->getTotalAmountConveyanceDetailsByExpense($employee, $yearMonth);

        $monthlyExpensePaticularAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getMonthlyExpenseParticularAmount($employee,$yearMonth);
        $crmConfig= $this->getDoctrine()->getRepository(CrmConfig::class)->findOneBy(['slug'=>'bike-miles-per-km','status'=>1]);

        $expenseChartByEmployee = $this->getDoctrine()->getRepository(ExpenseChart::class)->getExpenseChartByEmployee($employee?$employee->getId():null);
        $fixedDailyExpenseParticular = array_filter(array_map(function($n) { if($n['paymentDuration']=='DAILY' && $n['expensePaymentType']=='FIXED') return $n; }, $expenseChartByEmployee));

        $areaWiseExpenseParticular = [];
        if($fixedDailyExpenseParticular && sizeof($fixedDailyExpenseParticular)>0){
            foreach ($fixedDailyExpenseParticular as $expenseChart) {
                $areaWiseExpenseParticular['areaName'][$expenseChart['areaId']]=$expenseChart['areaName'];
                $areaWiseExpenseParticular['chartDetails'][$expenseChart['areaId']][$expenseChart['expenseChartDetailId']]=$expenseChart;
            }
        }

        $ratePerMilage = $employee->getExpenseChart() && $employee->getExpenseChart()->getTypeOfVehicle() && $employee->getExpenseChart()->getTypeOfVehicle() == 'car'? 0: 3.5;

        $typeOfVehicles = $this->typeOfVehicle($employee);

        $statusIsOneEntities = array_filter($entities, function($n) { if($n->getStatus()==1) return $n; });
        
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
            'areaWiseExpenseParticulars' => $areaWiseExpenseParticular,
            'conveyanceDetailsTotalAmount' => $conveyanceDetailsTotalAmount,
            'typeOfVehicles' => $typeOfVehicles,
            'statusIsOneEntities' => $statusIsOneEntities,
            'ratePerMilage' => $ratePerMilage,
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


        $crmConfig= $this->getDoctrine()->getRepository(CrmConfig::class)->findOneBy(['slug'=>'bike-miles-per-km','status'=>1]);
        if ($requestData && isset($requestData['expense']) && sizeof($requestData['expense'])>0) {
//            $expenseBatch = new ExpenseBatch();
            $existingExpenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);

            if($existingExpenseBatch){
                $expenseBatch = $existingExpenseBatch;
            }else{
                $expenseBatch = new ExpenseBatch();
                $expenseBatch->setEmployee($employee);
                $expenseBatch->setExpenseMonth(new \DateTime($expenseMonth));
            }

            $expenseBatch->setStatus(1);

            $totalReading = isset($requestData['totalRiding']) && $requestData['totalRiding'] != '' && $requestData['totalRiding'] > 0 ? $requestData['totalRiding'] : '0';
            $expenseBatch->setTotalRiding($totalReading);

            $perMilesAmount = $crmConfig && $crmConfig->getValue() && $crmConfig->getValue() > 0 ? $crmConfig->getValue() : 0;

            $expenseBatch->setPerMilesAmount($perMilesAmount);

            $expenseBatch->setTotalMilesAmount($totalReading * $perMilesAmount);


            $em = $this->getDoctrine()->getManager();
            $em->persist($expenseBatch);
            $em->flush();

            foreach ($requestData['expense'] as $expenseId => $expense) {
                /* @var Expense $expenseObj */
                $expenseObj = $this->getDoctrine()->getRepository(Expense::class)->find($expense);
                if($expenseObj->getExpenseBatch()==null){
                    $expenseObj->setExpenseBatch($expenseBatch);
                    $expenseObj->setStatus(2);
                }

                $em->persist($expenseObj);
            }
            $em->flush();

            $this->addFlash('success', 'Expense has been process successfully');

            return $this->redirectToRoute('crm_expense_details', ['employee' => $employee->getId(), 'monthYear' => $yearMonth]);
        }else{
            $this->addFlash('error', 'This month expense already process.');
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
     * Deletes a Expense entity.
     * @Route("/{expense}/{id}/delete", methods={"GET"}, name="crm_expense_particular_delete")
     * @return Response
     */
    public function expenseParticularDelete(Expense $expense, ExpenseParticular $expenseParticular): Response
    {
        $entity = $this->getDoctrine()->getRepository(ExpenseParticular::class)->findOneBy(['expense'=>$expense, 'id'=>$expenseParticular->getId()]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'Expense has been deleted successfully.');
        return $this->redirectToRoute('crm_expense_edit', ['id' => $expense->getId()]);
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
     *
     * @Route("/convence-details/update/inline/{id}", methods={"GET", "POST"}, name="crm_expense_convence_details", options={"expose"=true})
     * @return Response
     */
    public function expenseConvenceDetailInlineEdit(Request $request, ExpenseConveyanceDetails $expenseConveyanceDetails): Response
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if($expenseConveyanceDetails){
            $fieldName = 'set'.$data['name'];

            $expenseConveyanceDetails->$fieldName((int)$data['value']);

            $expenseConveyanceDetails->setTotalAmount($expenseConveyanceDetails->calculateTotalAmount());

            $em->persist($expenseConveyanceDetails);
            $em->flush();
            return new JsonResponse(['status'=>200, 'message'=>'Success', 'totalAmount'=>$expenseConveyanceDetails->getTotalAmount()]);
        }
        return new JsonResponse(['status'=>400, 'message'=>'Error']);
    }



    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/report", methods={"GET", "POST"}, name="crm_expense_report")
     * @param Request $request
     * @return Response
     */
    public function expenseReport(Request $request): Response
    {
        $form = $this->createForm(ExpenseReportSearchFormType::class, null, ['method' => 'POST']);

        $form->handleRequest($request);

        $yearMonth = date('Y-m');
        $entities = [];
        $expenseParticulars = [];
        $convenceDetails = [];

        $company = null;
        $companyName = '';
        $employeeIds = null;
        if ($form->isSubmitted()) {
            $filterBy = $form->getData();
            $data = $request->request->all();

            $yearMonth = isset($data['expense_report_search_form']['visitDate'])&&$data['expense_report_search_form']['visitDate']!=''?date('Y-m', strtotime('01-'.$data['expense_report_search_form']['visitDate'])):date('Y-m');
            $company = isset($data['expense_report_search_form']['company'])&&$data['expense_report_search_form']['company']!=''?$data['expense_report_search_form']['company']:null;

            // Nourish Agro, Nourish Feeds and Nourish Poultry & Hatchery are reported as
            // a single company, so the picker submits one value standing for all of them.
            // The three queries below take an id or a list, and filter with IN either way.
            if ($company === ExpenseReportSearchFormType::MERGED_COMPANY_VALUE) {
                $company = array_column(
                    $this->getDoctrine()->getRepository(Company::class)
                        ->createQueryBuilder('c')->select('c.id')->getQuery()->getArrayResult(),
                    'id'
                );
            }

            $employeeIds = isset($data['employeeId']) && sizeof($data['employeeId']) > 0 ? $data['employeeId'] : null;

            $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpensesByCompanyMonthYear($company, $yearMonth, $employeeIds);

            $expenseParticulars = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getExpenseParticularTotalAmount($company, $yearMonth, $employeeIds);

            $convenceDetails = $this->getDoctrine()->getRepository(ExpenseConveyanceDetails::class)->getConveyanceDetailsTotalAmount($company, $yearMonth, $employeeIds);



            /*if($entities && sizeof($entities)>0){
                foreach ($entities as $entity) {
                    $entities[$entity['employeeAutoId']]['particularTotalAmount']= isset($expenseParticulars[$entity['employeeAutoId']]) ? $expenseParticulars[$entity['employeeAutoId']]['totalAmount']: 0;
                    $entities[$entity['employeeAutoId']]['totalMileage']= isset($convenceDetails[$entity['employeeAutoId']]) ? $convenceDetails[$entity['employeeAutoId']]['totalMileage']: 0;
                    $entities[$entity['employeeAutoId']]['totalConvenceAmount']= isset($convenceDetails[$entity['employeeAutoId']]) ? $convenceDetails[$entity['employeeAutoId']]['totalAmount']: 0;
                }
            }*/
            // The merged group spans several core_company rows, so there is no single
            // record to take the heading from - name it from the one label instead.
            $companyName = is_array($company)
                ? ExpenseReportSearchFormType::MERGED_COMPANY_LABEL
                : (($found = $this->getDoctrine()->getRepository(Company::class)->find($company)) ? $found->getCompanyName() : '');
        }


        return $this->render('@TerminalbdCrm/expense/report.html.twig', [
            'form' => $form->createView(),
            'entities' => $entities,
            'company' => $company,
            'yearMonth' => $yearMonth,
            'convenceDetails' => $convenceDetails,
            'expenseParticulars' => $expenseParticulars,
            'employeeIds' => $employeeIds,
            'companyName' => $companyName,
        ]);

    }


    /**
     * @Route("/employee-expense-status", methods={"GET","POST"}, name="crm_employee_expense_status", options={"expose"=true})
     */
    public function employeeExpenseStatusForLineManager(Request $request)
    {
        $requestDate = $request->query->get('date')?date('Y-m-d', strtotime('01-'.$request->query->get('date'))):date('Y-m-01');
        $entities = [];
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(CrmTourPlanSearchFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        $lastDayOfMonth = date('Y-m-t');
        $employees=[];

        if ($form->isSubmitted()) {
            $filterBy = $form->getData();
            $employeeId = isset( $filterBy['employee']) &&  $filterBy['employee']!='' ? $filterBy['employee']->getId():null;

            $requestDate = isset($filterBy['visitDate'])?$filterBy['visitDate']->format("Y-m-d"):date('Y-m-01');

            $lastDayOfMonth = date('Y-m-t', strtotime($requestDate));
            $roleSplitArray = [];
            $userRoles = [];
            $employeeArray=[];

            foreach ($this->getUser()->getRoles() as $role) {
                $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
            }

            if (in_array('ADMIN', $roleSplitArray)) {
                if (in_array('ROLE_CRM_POULTRY_ADMIN', $this->getUser()->getRoles())) {
                    array_push($userRoles, 'ROLE_CRM_POULTRY_USER');
                }
                if (in_array('ROLE_CRM_CATTLE_ADMIN', $this->getUser()->getRoles())) {
                    array_push($userRoles, 'ROLE_CRM_CATTLE_USER');
                }
                if (in_array('ROLE_CRM_AQUA_ADMIN', $this->getUser()->getRoles())) {
                    array_push($userRoles, 'ROLE_CRM_AQUA_USER');
                }
                if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $this->getUser()->getRoles())) {
                    array_push($userRoles, 'ROLE_CRM_SALES_MARKETING_USER');
                }
                $employeeArray = $this->getDoctrine()->getRepository(User::class)->getRoleWiseEmployees($userRoles, $employeeId);
            }elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $this->getUser()->getRoles())){
                $employeeArray = $this->getDoctrine()->getRepository(User::class)->getEmployeesByEmployeeIds($this->getUser(), $employeeId);
            }
            $uniqueEmployees = [];
            if(isset($employeeArray['employee']) && sizeof($employeeArray['employee'])>0){
                $uniqueEmployees = $this->unique_array($employeeArray['employee'], 'id');
            }
            if(sizeof($uniqueEmployees)>0){
                foreach ($uniqueEmployees as $employee) {
                    $employees[$employee['lineManagerId']][] = $employee;
                }
            }

            if($requestDate){
                $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpenseStatusByEmployeeAndDate( $employeeId, date('Y-m', strtotime($requestDate)), $this->getUser() );
            }
        }

        $start = new \DateTime($requestDate);
        $end = new \DateTime($lastDayOfMonth);
        $arrayDays = [];

        while ($start <= $end) {
            $arrayDays[$start->format('Y-m-d')] = $start->format('d');
            $start->modify('+1 day');
        }
//        dd($employees);
        return $this->render('@TerminalbdCrm/expense/employee-expense-status-for-line-manager.html.twig', [
            'entities' => $entities,
            'requestDate' => date('m-Y', strtotime($requestDate)),
            'currentDate' => date('Y-m-d'),
            'form' => $form->createView(),
            'arrayDays' => $arrayDays,
            'employees' => $employees
        ]);
    }


    public function unique_array($my_array, $key) {
        $result = array();   // Initialize an empty array to store the unique values
        $i = 0;              // Initialize a counter
        $key_array = array(); // Initialize an array to keep track of encountered keys

        // Iterate through each element in the input array
        foreach($my_array as $val) {
            // Check if the key value is not already present in the key array
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];  // Store the key value in the key array
                $result[$i] = $val;           // Store the entire element in the result array
            }
            $i++;  // Increment the counter
        }

        // Return the array containing unique values based on the specified key
        return $result;
    }

}
