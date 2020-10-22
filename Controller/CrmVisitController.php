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
use Symfony\Component\HttpFoundation\JsonResponse;
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
            ->add('SaveAndCreate', SubmitType::class)
            ->add('Save', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
//            $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertDailyActivity($entity,$data);
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('new_visit');
            }
            return $this->redirectToRoute('crm_visit');
        }
        $agent=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($entity->getEmployee());
        $purpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'PURPOSE'));
        $agentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'AGENT_PURPOSE'));
        $otherAgentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'OTHER_AGENT_PURPOSE'));
        $subAgentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SUB_AGENT_PURPOSE'));
        $lifeCycleReport =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'LIFE_CYCLE_REPORT'));
        $farmers =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'farmer');
        $subAgents =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'sub-agent');
        $otherAgents =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'other-agent');


        return $this->render('@TerminalbdCrm/crmvisit/new.html.twig', [
            'entity' => $entity,
            'purposes'=>$purpose,
            'agentPurposes'=>$agentPurpose,
            'otherAgentPurposes'=>$otherAgentPurpose,
            'subAgentPurposes'=>$subAgentPurpose,
            'lifeCycleReport'=>$lifeCycleReport,
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
     * Add a CrmVisit entity.
     * @Route("/details/add", methods={"POST"}, name="crm_visit_item_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function CRMDetailsAdd(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new CrmVisitDetails();
        $crmVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->find($request->request->get('crm_visit_id'));
        $farmer=null;
        if($request->request->get('farmer')){
            $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($request->request->get('farmer'));
            $entity->setCrmCustomer($farmer?$farmer:null);
        }
        $agent=null;
        if($request->request->get('agent')){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($request->request->get('agent'));
            $entity->setAgent($agent?$agent:null);
        }

        $purpose = $this->getDoctrine()->getRepository(Setting::class)->find($request->request->get('purpose'));

        $entity->setCrmVisit($crmVisit?$crmVisit:null);
        $entity->setPurpose($purpose?$purpose:null);
        $entity->setFarmCapacity($request->request->get('farmer_capacity')?$request->request->get('farmer_capacity'):null);
        $entity->setComments($request->request->get('comments'));
        $entity->setProcess($request->request->get('process'));
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.added_successfully');
        return new JsonResponse(array(
            'message'=>'Successfully',
            'status'=>200
        ));
    }
    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("/{id}/{process}/item/refresh", methods={"GET", "POST"}, name="crm_visit_item_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function CRMDetailsRefresh($id, $process='farmer'): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmVisit::class)->find($id);

        return $this->render('@TerminalbdCrm/crmvisit/partial/'.$process.'_information.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * Deletes a CrmVisit entity.
     * @Route("/item/{id}/delete", methods={"GET"}, name="crm_visit_item_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function itemDelete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return new JsonResponse(array(
            'message'=>$id,
            'status'=>200
        ));
    }







}