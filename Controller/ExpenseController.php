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
use Terminalbd\CrmBundle\Entity\Expense;
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
            $em = $this->getDoctrine()->getManager();

            $entity->setStatus(1);

            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Expense has been updated successfully');
//            return new JsonResponse(['status'=>200,'message'=>'Expense has been updated successfully']);
            return $this->redirectToRoute('crm_expense_edit', ['id'=>$entity->getId()]);
        }
        return $this->render('@TerminalbdCrm/expense/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
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
}
