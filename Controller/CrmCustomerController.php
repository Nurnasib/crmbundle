<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

/**
 * @Route("/crm/customer")
 */

class CrmCustomerController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="crm_customer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index()
    {
        $entities=$this->getDoctrine()->getRepository(CrmCustomer::class)->findAll();
        $webPath = $this->get('kernel')->getProjectDir();
//        var_dump($webPath);die;
        return $this->render('@TerminalbdCrm/crmcustomer/index.html.twig', [
            'entities' =>$entities
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="crm_customer_new")
     */
    public function new(Request $request): Response
    {

        $entity = new CrmCustomer();
        $data = $request->request->all();

        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(CrmCustomerFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_customer_new');
            }
            return $this->redirectToRoute('crm_customer');
        }
        return $this->render('@TerminalbdCrm/crmcustomer/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_customer_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, CrmCustomer $entity): Response
    {
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(CrmCustomerFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_customer', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('crm_customer');
        }
        return $this->render('@TerminalbdCrm/crmcustomer/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param Request $request
     * @Route("/{id}/farmer-create/ajax" ,name="new_farmer_ajax", methods={"POST"}, options={"expose"=true})
     */
    public function createFarmer(Request $request,$id){

        $entity=new CrmCustomer();
        $allRequestData = $request->request->all();
        $group = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('slug'=>'farmer'));
        $feed = $this->getDoctrine()->getRepository(Setting::class)->find($allRequestData['feed_id']);
        $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
        $agent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['agent']);
        $otherAgent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['other_agent']);
        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setCustomerGroup($group);
        $entity->setLocation($location);
        $entity->setAgent($agent?$agent:$otherAgent);
        $entity->setOtherAgent($otherAgent?$otherAgent:$agent);
        $em=$this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $returnData= array(
          'id'=>$entity->getId(),
          'name'=>$entity->getName(),
        );
//        $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertCrmVisitDetailForFarmer($entity, $id, $allRequestData);
//        $this->getDoctrine()->getRepository(FarmerTouchReport::class)->insertFarmerTouch($entity, $this->getUser(), $feed, $allRequestData);
        $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->insertCrmFarmerIntroduceDetails($entity, $this->getUser(), $feed, $allRequestData);
        return new JsonResponse(array($returnData));

    }

    /**
     * @param Request $request
     * @Route("/{id}/other-create/ajax" ,name="new_other_agent_ajax", methods={"POST"}, options={"expose"=true})
     */
    public function createOtherAgent(Request $request,$id){

//        $entity=new CrmCustomer();
        $entity=new Agent();
        $allRequestData = $request->request->all();
        $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug'=>'other-agent'));
        $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setAgentGroup($group);
        $entity->setUpozila($location);
        $entity->setDistrict($location->getParent());
        $entity->setCreated(new \DateTime());
        $em=$this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $entity->setAgentId($entity->getId());
        $em->persist($entity);
        $em->flush();
        $returnData= array(
            'status'=>200,
          'id'=>$entity->getId(),
          'name'=>$entity->getName(),
        );
        $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertOtherAgent($entity,$id,$allRequestData);
        return new JsonResponse(array($returnData));

    }

    /**
     * @param Request $request
     * @Route("/{id}/sub-create/ajax" ,name="new_sub_agent_ajax", methods={"POST"}, options={"expose"=true})
     * @return JsonResponse
     */
    public function createSubAgent(Request $request,$id)
    {

        $entity = new Agent();
        $allRequestData = $request->request->all();

        $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug'=>'sub-agent'));
        $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);

        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setAgentGroup($group);
        $entity->setUpozila($location);
        $entity->setDistrict($location->getParent());
        $entity->setCreated(new \DateTime('now'));
        if($allRequestData['agent']){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['agent']);
            $entity->setParent($agent);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        $returnData= array(
            'status'=>200,
            'id' => $entity->getId(),
            'name' => $entity->getName(),
        );
        if ($allRequestData['purpose']){
            $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertSubAgent($entity, $id, $allRequestData);

        }
        return new JsonResponse(array($returnData));

    }

    /**
     * Deletes a CrmCustomer entity.
     * @Route("/{id}/delete", methods={"GET"}, name="customer_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @param CrmCustomer $crmCustomer
     * @Route("/{id}/ajax" ,name="get_farmer_ajax", methods={"GET"}, options={"expose"=true})
     */
    public function getCustomerById(CrmCustomer $crmCustomer){

        $returnData= array(
            'id'=>$crmCustomer->getId(),
            'name'=>$crmCustomer->getName(),
            'address'=>$crmCustomer->getAddress(),
            'phone'=>$crmCustomer->getMobile(),
        );
        return new JsonResponse(array($returnData));

    }

    /**
     * Deletes a Setting entity.
     * @param Agent $agent
     * @Route("/{id}/find/ajax", methods={"GET"}, name="get_core_agent_find_ajax", options={"expose"=true})
     */
    public function getAgentByIdUsingAjax(Agent $agent): Response
    {
        $returnData= array(
            'id'=>$agent->getId(),
            'name'=>$agent->getName(),
            'address'=>$agent->getAddress(),
            'mobile'=>$agent->getMobile(),
            'agentId'=>$agent->getAgentId(),
        );
        return new JsonResponse(array($returnData));

    }


    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("species/name/by/parent/{id}", methods={"GET", "POST"}, name="species_name_by_parent_id_ajax", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function farmerIntroduceDetails($id): Response
    {
        $farmerType =$this->getDoctrine()->getRepository(Setting::class)->find($id);
        $speciesTypes =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SPECIES_TYPE','status'=>1, 'parent'=>$id));

        return $this->render('@TerminalbdCrm/crmcustomer/farmer-introduce-details.html.twig', [
            'speciesTypes' => $speciesTypes,
            'farmerType' => $farmerType,
        ]);
    }



}