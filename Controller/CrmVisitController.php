<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;


use App\Entity\Core\Agent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Terminalbd\CrmBundle\Form\CrmVisitFormType;
use Terminalbd\CrmBundle\Repository\CrmVisitRepository;

/**
 * @Route("/crm/visit")
 */

class CrmVisitController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="crm_visit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index()
    {
        $entities= $this->getDoctrine()->getRepository(CrmVisit::class)->findAll();
        return $this->render('@TerminalbdCrm/crmvisit/index.html.twig',[
            'entities' => $entities
        ]);
    }

    /**
     * @param Request $request
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new" ,name="new_visit")
     */
    public function new(Request $request){

        $entity = new CrmVisit();
        $em = $this->getDoctrine()->getManager();
        $entity->setEmployee($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirectToRoute('crm_visit_edit',array('id'=>$entity->getId()));

    }


    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_visit_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, CrmVisit $entity): Response
    {
        $data = $request->request->all();
        $form = $this->createForm(CrmVisitFormType::class, $entity,array('user' => $this->getUser()))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertDailyActivity($entity,$data);
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_visit', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('crm_visit');
        }
        $agent=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($entity->getEmployee());
        $purpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'PURPOSE'));
        $farmers =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'farmer');
        $subAgents =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'sub-agent');
        $otherAgents =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'other-agent');


        return $this->render('@TerminalbdCrm/crmvisit/new.html.twig', [
            'entity' => $entity,
            'purposes'=>$purpose,
            'agents'=>$agent,
            'farmers'=>$farmers,
            'subAgents'=>$subAgents,
            'otherAgents'=>$otherAgents,
            'form' => $form->createView(),
        ]);
    }



    /**
     * Deletes a CrmVisit entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_visit_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmVisit::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * Deletes a CrmVisit entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_visit_item_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function itemDelete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }







}