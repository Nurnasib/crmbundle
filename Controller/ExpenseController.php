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
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ExpenseFormType;
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
        return $this->render('@TerminalbdCrm/expense/index.html.twig',[
            'entities' => $entities
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
            $existingExpenseCheck=$this->getDoctrine()->getRepository(Expense::class)->getExpenseByEmployeeAndDate($entity, $this->getUser(),$expenseDate);
            if($existingExpenseCheck && sizeof($existingExpenseCheck)>0) {

                $this->addFlash('error', $expenseDate.' date expense already exist.');
                return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
            }



            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $entity->setStatus(1);

            $em->persist($entity);

            if($_FILES['files']['size'][0] != 0 && $_FILES['files']['error'][0] == 0){
                $files = empty($_FILES['files']) ? '' : $_FILES['files'];
                $this->getDoctrine()->getRepository(DmsFile::class)->insertAttachmentFile($entity, $data, $files, 'CRM','Expense');
            }

            $em->flush();
            $this->addFlash('success', 'Expense has been updated successfully');
            return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
        }
        $attchments = $this->getDoctrine()->getRepository(DmsFile::class)->getDmsAttchmentFile($entity,'CRM','Expense');
        return $this->render('@TerminalbdCrm/expense/new.html.twig', [
            'entity' => $entity,
            'attchments' => $attchments,
            'form' => $form->createView(),
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
        $expenseMonth = date('Y-m-d',strtotime($yearMonth.'-01'));
        $expenseBatch= $this->getDoctrine()->getRepository(ExpenseBatch::class)->findOneBy(['employee'=>$employee, 'expenseMonth'=>new \DateTime($expenseMonth)]);

        return $this->render('@TerminalbdCrm/expense/details.html.twig', [
            'entities' => $entities,
            'employee' => $employee,
            'yearMonth' => $yearMonth,
            'expenseBatch' => $expenseBatch,
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

        $expenseBatch= new ExpenseBatch();

        $expenseBatch->setEmployee($employee);
        $expenseBatch->setStatus(1);
        $expenseBatch->setExpenseMonth(new \DateTime($expenseMonth));
        $expenseBatch->setTotalRiding(isset($requestData['totalRiding'])&&$requestData['totalRiding']!=''&&$requestData['totalRiding']>0?$requestData['totalRiding']:'0');
        $expenseBatch->setMaintenace(isset($requestData['monthlyMaintenace'])&&$requestData['monthlyMaintenace']!=''&&$requestData['monthlyMaintenace']>0?$requestData['monthlyMaintenace']:'0');
        $expenseBatch->setOthers(isset($requestData['monthlyOthers'])&&$requestData['monthlyOthers']!=''&&$requestData['monthlyOthers']>0?$requestData['monthlyOthers']:'0');

        $em = $this->getDoctrine()->getManager();
        $em->persist($expenseBatch);
        $em->flush();

        if ($requestData && isset($requestData['expense']) && sizeof($requestData['expense'])>0){
            foreach ($requestData['expense'] as $expenseId=>$expense) {
                /* @var Expense $expenseObj*/
                $expenseObj = $this->getDoctrine()->getRepository(Expense::class)->find($expense);

                $expenseObj->setExpenseBatch($expenseBatch);
                $expenseObj->setStatus(2);

                $em->persist($expenseObj);
                $em->flush();
            }

        }


        return $this->redirectToRoute('crm_expense_details', ['employee'=>$employee->getId(),'monthYear'=>$yearMonth]);

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
        $this->addFlash('success', 'post.deleted_successfully');
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
}
