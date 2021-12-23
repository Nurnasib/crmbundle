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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
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
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index()
    {
        $reports = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'FARMER_REPORT','slug'=>['fcr-after-sale-boiler','fcr-after-sale-sonali']]);

        $entities= $this->getDoctrine()->getRepository(CrmVisit::class)->findBy(array('employee'=>$this->getUser()),['created' => 'DESC']);
        return $this->render('@TerminalbdCrm/crmvisit/index.html.twig',[
            'entities' => $entities,
            'reports' => $reports,
        ]);
    }

    /**
     * @param Request $request
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new" ,name="new_visit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function new(Request $request){

        $entity = new CrmVisit();

        $form = $this->createForm(CrmVisitFormType::class, $entity,array('user' => $this->getUser()))
            ->add('Save', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirectToRoute('crm_visit_edit',array('id'=>$entity->getId()));
        }

        return $this->render('@TerminalbdCrm/crmvisit/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
            'employee' => $this->getUser(),
        ]);

    }


    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_visit_edit")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @param Request $request
     * @param CrmVisit $entity
     * @return Response
     */

    public function edit(Request $request, CrmVisit $entity): Response
    {
        $data = $request->request->all();
        $reports = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'FARMER_REPORT','slug'=>['fcr-after-sale-boiler','fcr-after-sale-sonali','company-species-wise-average-fcr-after']]);

        $form = $this->createForm(CrmVisitFormType::class, $entity,array('user' => $this->getUser()))
            ->add('Save', SubmitType::class);
        $form->remove('location');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');

            return $this->redirectToRoute('crm_visit');
        }
        $agent=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($entity->getEmployee());
        $purpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'PURPOSE'));
        $agentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'AGENT_PURPOSE'));
        $otherAgentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'OTHER_AGENT_PURPOSE'));
        $subAgentPurpose =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SUB_AGENT_PURPOSE'));
        $lifeCycleReport =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FARMER_REPORT'));
        $firmTypes =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FARM_TYPE','status'=>1));
        $breedTypes =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE','status'=>1));
        $feedCompanies =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_NAME','status'=>1),array('name'=>'ASC'));
        $breedNames =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_NAME','status'=>1));
        $farmers =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($entity->getEmployee(),'farmer');
        $subAgents =$this->getDoctrine()->getRepository(Agent::class)->getLocationAndGroupWise($entity->getEmployee(),'sub-agent');
        $otherAgents =$this->getDoctrine()->getRepository(Agent::class)->getLocationAndGroupWise($entity->getEmployee(),'other-agent');

        $firmTypesArray=array();

        foreach ($firmTypes as $firmType){
            $firmTypesArray[$firmType->getParent()->getName()][]=$firmType;
        }

        if($this->getUser()->getServiceMode() && $this->getUser()->getServiceMode()->getSlug()=='sales-marketing'){
            $visitId = $entity->getId();
            $regions = $this->getDoctrine()->getRepository(Location::class)->findBy(['level' => 3]);
            $breedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'MEAT_EGG_TYPE']);
            $price = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->processPrice($regions,$breedTypes, $visitId);
//            dd($price);
            return $this->render('@TerminalbdCrm/crmvisit/edit-sales-marketing.html.twig', [
                'entity' => $entity,
                'purposes'=>$purpose,
                'agentPurposes'=>$agentPurpose,
                'otherAgentPurposes'=>$otherAgentPurpose,
                'subAgentPurposes'=>$subAgentPurpose,
//                'lifeCycleReport'=>$lifeCycleReport,
//                'firmTypes'=>$firmTypesArray,
//                'breedTypes'=>$breedTypes,
//                'breedNames'=>$breedNames,
                'feedCompanies'=>$feedCompanies,
                'breedNames'=>$breedNames,
                'agents'=>$agent,
                'farmers'=>$farmers,
                'subAgents'=>$subAgents,
                'otherAgents'=>$otherAgents,
                'regions'=>$regions,
                'breedTypes'=>$breedTypes,
                'price'=>$price,
                'form' => $form->createView(),
//                'fcr_after_reports' => $reports,
            ]);
        }


        return $this->render('@TerminalbdCrm/crmvisit/edit.html.twig', [
            'entity' => $entity,
            'purposes'=>$purpose,
            'agentPurposes'=>$agentPurpose,
            'otherAgentPurposes'=>$otherAgentPurpose,
            'subAgentPurposes'=>$subAgentPurpose,
            'lifeCycleReport'=>$lifeCycleReport,
            'firmTypes'=>$firmTypesArray,
            'breedTypes'=>$breedTypes,
            'breedNames'=>$breedNames,
            'feedCompanies'=>$feedCompanies,
            'agents'=>$agent,
            'farmers'=>$farmers,
            'subAgents'=>$subAgents,
            'otherAgents'=>$otherAgents,
            'form' => $form->createView(),
            'fcr_after_reports' => $reports,
        ]);
    }



    /**
     * Deletes a CrmVisit entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_visit_delete")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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
            $entity->setAgent($entity->getCrmCustomer()->getAgent());
        }

        if($request->request->get('process')=='agent' && $request->request->get('agent')!=''){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($request->request->get('agent'));
            $entity->setAgent($agent?$agent:null);
        }
        if($request->request->get('process')=='other-agent' && $request->request->get('agent')!=''){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($request->request->get('agent'));
            $entity->setAgent($agent?$agent:null);
        }

        $purpose = $this->getDoctrine()->getRepository(Setting::class)->find($request->request->get('purpose'));

        if($request->request->get('farmer_firm_type')!=''){
            $farmer_firm_type = $this->getDoctrine()->getRepository(Setting::class)->find($request->request->get('farmer_firm_type'));
            $entity->setFirmType($farmer_firm_type?$farmer_firm_type:null);
        }

        if($request->request->get('farmer_report')!=''){
            $farmer_report = $this->getDoctrine()->getRepository(Setting::class)->find($request->request->get('farmer_report'));
            $entity->setReport($farmer_report?$farmer_report:null);
        }

        $entity->setCrmVisit($crmVisit?$crmVisit:null);
        $entity->setPurpose($purpose?$purpose:null);
        $entity->setFarmCapacity($request->request->get('farmer_capacity')!=''?$request->request->get('farmer_capacity'):null);

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
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function CRMDetailsRefresh($id, $process='farmer'): Response
    {
        $reports = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'FARMER_REPORT','slug'=>['fcr-after-sale-boiler','fcr-after-sale-sonali','company-species-wise-average-fcr-after']]);

        $entity = $this->getDoctrine()->getRepository(CrmVisit::class)->find($id);
        $lifeCycleReport =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FARMER_REPORT'));
        $breedTypes =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE','status'=>1));

        return $this->render('@TerminalbdCrm/crmvisit/partial/'.$process.'_information.html.twig', [
            'entity' => $entity,
            'lifeCycleReport' => $lifeCycleReport,
            'breedTypes' => $breedTypes,
            'fcr_after_reports' => $reports,
        ]);
    }

    /**
     * Deletes a CrmVisit entity.
     * @Route("/item/{id}/delete", methods={"GET"}, name="crm_visit_item_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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

    /**
     * @Route("/report/{id}/ajax", methods={"GET"}, name="crm_report_farm_type_ajax", options={"expose"=true})
     */
    public function getReportByFarmType($id): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1, 'settingType'=>'FARMER_REPORT','parent'=>$id));
        $exceptSlug=['fcr-after-sale-boiler','fcr-after-sale-sonali','company-species-wise-average-fcr-after'];
        $arrayData = array();
        /**@var Setting $entity*/
        foreach ($entities as $entity){
            if(!in_array($entity->getSlug(),$exceptSlug)){
                $arrayData[]=array('id'=>$entity->getId(),'name'=>$entity->getName());
            }

        }

        return new JsonResponse($arrayData);
    }

    /**
     * @Route("/update/{id}/meat-egg-price", name="crm_visit_meat_egg_price_update", options={"expose"=true})
     */
    public function updateMeatAndEggPrice(Request $request)
    {
        $visitId = $_REQUEST['visitId'];
        $regionId = $_REQUEST['regionId'];
        $breedTypeId = $_REQUEST['breedTypeId'];
        $price = $_REQUEST['price'];

        $entity = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->findOneBy(['crmVisit' => $visitId, 'region' => $regionId, 'breedType' => $breedTypeId]);

        if($entity){
            $em = $this->getDoctrine()->getManager();
            $entity->setPrice($price);
            $em->persist($entity);
            $em->flush();
            return new JsonResponse('Success');
        }
        return new JsonResponse('Failed');

    }

}