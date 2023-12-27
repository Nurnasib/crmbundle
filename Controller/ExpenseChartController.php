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
use Terminalbd\CrmBundle\Entity\ExpenseChartDetail;
use Terminalbd\CrmBundle\Entity\ExpenseParticular;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ExpenseChartFormType;
use Terminalbd\CrmBundle\Form\ExpenseFormType;
use Terminalbd\CrmBundle\Form\ExpenseVehicleFormType;
use Terminalbd\CrmBundle\Form\SettingFormType;

/**
 * @Route("/crm/expense-chart")
 * @Security("is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class ExpenseChartController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_expense_chart")
     * @return Response
     */
    public function index(): Response
    {
        $entities = $this->getDoctrine()->getRepository(ExpenseChart::class)->findAll();
//        $dailyExpenseParticularAttributes = $this->getDoctrine()->getRepository(Setting::class)->getDailyExpenseParticular();
//        $expensePaticularTotalAmount = $this->getDoctrine()->getRepository(ExpenseParticular::class)->getTotalAmountExpenseParticular($this->getUser());
        return $this->render('@TerminalbdCrm/expenseChart/index.html.twig',[
            'entities' => $entities,
//            'expensePaticularTotalAmount' => $expensePaticularTotalAmount,
//            'expenseParticularAttributes' => $dailyExpenseParticularAttributes,
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_expense_chart_new")
     * @return Response
     */
    public function new(Request $request): Response
    {
        $entity = new ExpenseChart();

        $form = $this->createForm(ExpenseChartFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $designation=$form->getData()->getDesignation();

            $existingExpenseChart=$this->getDoctrine()->getRepository(ExpenseChart::class)->findOneBy(['designation'=>$designation]);
            if($existingExpenseChart){
                $this->addFlash('error', 'This Designation expense chart already exist.');
                return $this->redirectToRoute('crm_expense_chart_new');
            }
            $requestDataAmount = $request->request->get('amount');

            if ($requestDataAmount && sizeof($requestDataAmount) > 0) {
                foreach ($requestDataAmount as $particularId => $amount) {
                    $particularObj = $this->getDoctrine()->getRepository(Setting::class)->find($particularId);
                    $requestAmount = $amount;
                    $amount = $requestAmount && $requestAmount != '' ? $requestAmount : null;

                    $expenseChartDetail = new ExpenseChartDetail();

                    $expenseChartDetail->setAmount($amount);
                    $expenseChartDetail->setExpenseChart($entity);
                    $expenseChartDetail->setParticular($particularObj ? $particularObj : null);

                    $em->persist($expenseChartDetail);
                }
            }

            $entity->setCreatedBy($this->getUser());

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Expense chart has been created successfully');

            return $this->redirectToRoute('crm_expense_chart');
        }

        $dailyExpenseParticulars = $this->getDoctrine()->getRepository(Setting::class)->getDailyExpenseParticular();


        return $this->render('@TerminalbdCrm/expenseChart/new.html.twig', [
            'dailyExpenseParticulars' => $dailyExpenseParticulars,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_expense_chart_edit")
     * @param Request $request
     * @param Expense $entity
     * @return Response
     */

    public function edit(Request $request, ExpenseChart $entity): Response
    {
        $form = $this->createForm(ExpenseChartFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $designation=$form->getData()->getDesignation();

            $existingExpenseChart=$this->getDoctrine()->getRepository(ExpenseChart::class)->findOneBy(['designation'=>$designation]);
            if($existingExpenseChart && $existingExpenseChart->getId()!=$entity->getId()){
                $this->addFlash('error', 'This Designation expense chart already exist.');
                return $this->redirectToRoute('crm_expense_chart_edit', ['id'=>$entity->getId()]);
            }

            $em->persist($entity);

            $data = $request->request->get('amount');

            if($data && sizeof($data)>0){
                foreach ($data as $particularId=>$amount) {
                    $particularObj=$this->getDoctrine()->getRepository(Setting::class)->find($particularId);

                    $existingExpenseChartDetail=$this->getDoctrine()->getRepository(ExpenseChartDetail::class)->findOneBy(['expenseChart'=>$entity, 'particular'=>$particularObj]);

                    if($existingExpenseChartDetail){
                        $expenseParticular=$existingExpenseChartDetail;
                    }else{
                        $expenseParticular= new ExpenseChartDetail();
                    }

                    $expenseParticular->setAmount($amount);
                    $expenseParticular->setExpenseChart($entity);
                    $expenseParticular->setParticular($particularObj?$particularObj:null);
                    $em->persist($expenseParticular);
                }
            }

            $entity->setUpdatedBy($this->getUser());

            $em->flush();
            $this->addFlash('success', 'Expense chart has been updated successfully');
            return $this->redirectToRoute('crm_expense_chart_edit', ['id'=>$entity->getId()]);
        }
        $dailyExpenseParticulars = $this->getDoctrine()->getRepository(Setting::class)->getDailyExpenseParticular();

        $expenseChartDetailByExpenseChart = $this->getDoctrine()->getRepository(ExpenseChartDetail::class)->getExpenseChartDetailByExpenseChart($entity->getId());

        return $this->render('@TerminalbdCrm/expenseChart/edit.html.twig', [
            'entity' => $entity,
            'dailyExpenseParticulars' => $dailyExpenseParticulars,
            'expenseChartDetailByExpenseChart' => $expenseChartDetailByExpenseChart,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Deletes a Expense entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_expense_chart_delete")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(ExpenseChart::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'Expense chart has been deleted successfully.');
        return new Response('Success');
    }


}
