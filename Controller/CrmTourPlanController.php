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
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\CrmVisitPlan;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmTourPlanEditFormType;
use Terminalbd\CrmBundle\Form\CrmTourPlanFormType;
use Terminalbd\CrmBundle\Form\CrmTourPlanSearchFormType;
use Terminalbd\CrmBundle\Form\CrmVisitFormType;

/**
 * @Route("/crm/tour/plan")
 * @Security("is_granted('ROLE_CORE') or is_granted('ROLE_USER') or is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_DEVELOPER')")
 */
class CrmTourPlanController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_tour_plan", options={"expose"=true})
     */
    public function index(Request $request)
    {
        $requestDate = $request->query->get('date')?date('Y-m-d', strtotime('01-'.$request->query->get('date'))):date('Y-m-01');
        $entities = $this->getDoctrine()->getRepository(CrmVisitPlan::class)->getMonthlyTourPlanByEmployeeAndDate($this->getUser()->getId(), date('Y-m', strtotime($requestDate)), 'monthly');
//        dd($entities);
//        dd($requestDate);
        return $this->render('@TerminalbdCrm/crmTourPlan/index.html.twig', [
            'entities' => $entities,
            'requestDate' => date('m-Y', strtotime($requestDate)),
            'currentDate' => date('Y-m-d')
        ]);
    }
    
    /**
     * @Route("/employee-tour-plan", methods={"GET","POST"}, name="crm_employee_tour_plan", options={"expose"=true})
     */    
    public function employeeTourPlanForLineManager(Request $request)
    {
        $requestDate = $request->query->get('date')?date('Y-m-d', strtotime('01-'.$request->query->get('date'))):date('Y-m-01');
        $entities = [];
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(CrmTourPlanSearchFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $filterBy = $form->getData();
            $employeeId = isset( $filterBy['employee']) &&  $filterBy['employee']!='' ? $filterBy['employee']->getId():null;
            $requestDate = isset($filterBy['visitDate'])?$filterBy['visitDate']->format("Y-m-d"):date('Y-m-01');
            if($employeeId && $requestDate){
                $entities = $this->getDoctrine()->getRepository(CrmVisitPlan::class)->getMonthlyTourPlanByEmployeeAndDate($employeeId, date('Y-m', strtotime($requestDate)), 'monthly');
            }
        }
        return $this->render('@TerminalbdCrm/crmTourPlan/employee-tour-plan-for-line-manager.html.twig', [
            'entities' => $entities,
            'requestDate' => date('m-Y', strtotime($requestDate)),
            'currentDate' => date('Y-m-d'),
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * @Route("/create", methods={"GET", "POST"}, name="crm_tour_plan_create", options={"expose"=true})
     */
    public function create(Request $request)
    {
        $visit = new CrmVisitPlan();
        $form = $this->createForm(CrmTourPlanFormType::class, $visit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = $request->request->get('crm_tour_plan_form');

            $monthYear = date('Y-m-d', strtotime('01-'.$date['visitDate']));

            $startDate = isset( $date['visitDate']) &&  $date['visitDate'] !=''? (new \DateTime( $monthYear))->format('t'): date('t');
            $em = $this->getDoctrine()->getManager();
            for($i=1; $i<=$startDate; $i++){
               $visitingDate =  new \DateTime( date('Y-m-d', strtotime($monthYear)));

                $existings = $this->getDoctrine()->getRepository(CrmVisitPlan::class)->findOneBy(['visitDate'=>$visitingDate, 'employee'=>$this->getUser()]);
                if($existings){
                    continue;
                }
                $visit = new CrmVisitPlan();
                $visit->setVisitDate(new \DateTime( date('Y-m', strtotime($monthYear)).'-'.$i));
                $visit->setEmployee($this->getUser());
                $visit->setCreatedAt(new \DateTime());
                $em->persist($visit);
            }
            $em->flush();
            $this->addFlash('success', 'Tour Plan Created Successfully');
            return $this->redirectToRoute('crm_tour_plan');
        }
        return $this->render('@TerminalbdCrm/crmTourPlan/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/update/{id}", methods={"GET", "POST"}, name="crm_tour_plan_update", options={"expose"=true})
     */
    public function update(Request $request, CrmVisitPlan $visitPlan)
    {
        $form = $this->createForm(CrmTourPlanEditFormType::class, $visitPlan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestVistingArea = $request->request->get('area_list');
            $location = $this->getDoctrine()->getRepository(Location::class)->getLocationByIds($requestVistingArea);
//            $agents = $this->getDoctrine()->getRepository(Agent::class)->getAgentByIds($request->request->get('agent_list'));

            $visitPlan->setAreaList($location && count($location)>0?$location:null);
//            $visitPlan->setAgentList($agents && count($agents)>0?$agents:null);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tour Plan Updated Successfully');
            return $this->redirectToRoute('crm_tour_plan', ['date'=>date('m-Y', strtotime($visitPlan->getVisitDate()->format('Y-m-d')))]);
        }

        if($visitPlan->getVisitDate()->format('Y-m') < date('Y-m')){
            $this->addFlash('error', 'You can not edit past date tour plan');
            return $this->redirectToRoute('crm_tour_plan');
        }

        $visitAreaList = $this->getLocationByEmployee();

//        $agentList = $this->getDoctrine()->getRepository(Agent::class)->getLocationWiseAgentForm($this->getUser());

        return $this->render('@TerminalbdCrm/crmTourPlan/edit.html.twig', [
            'form' => $form->createView(),
            'visitAreaList' => $visitAreaList,
//            'agentList' => $agentList,
            'addedVisitArea' => $visitPlan->getAreaList() && sizeof($visitPlan->getAreaList())>0? array_column($visitPlan->getAreaList(), 'areaId'):[],
//            'addedAgent' => $visitPlan->getAgentList() && sizeof($visitPlan->getAgentList())>0? array_column($visitPlan->getAgentList(), 'id'):[]
        ]);
    }


    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @Route("/working-mode-inline-update/{id}", name="working_mode_inline_update", options={"expose"=true})
     */
    public function inlineUpdateDesignation(Request $request, CrmVisitPlan $visitPlan)
    {

        $data = $request->request->all();

        if (!$visitPlan) {
            throw $this->createNotFoundException('Unable to find User');
        }
        if($visitPlan->getWorkingMode() && $visitPlan->getWorkingMode()->getId()==$data['value']){
            return new JsonResponse(['status' => 200]);
        }else{
            $workingMode = $this->getDoctrine()->getRepository(Setting::class)->find($data['value']);
            $visitPlan->setWorkingMode($workingMode? $workingMode:null);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['status' => 200]);
        }
    }


    /**
     * @Route("/{id}/update-inline-visiting_area", methods={"GET", "POST"}, name="inline_visiting_area_using_ajax", options={"expose"=true})
     * @param CrmVisitPlan $visitPlan
     * @param Request $request
     * @return Response
     */
    public function updateInlineVisitingArea(CrmVisitPlan $visitPlan, Request $request): Response
    {
        $visitingArea = $request->request->get('value');

        if($visitPlan && $visitingArea && $visitPlan->getVisitingArea() != $visitingArea){
            $visitPlan->setVisitingArea($visitingArea);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse([
                'status' => 200,
                'message' => 'success'
            ]);

        }
        return new JsonResponse([
            'status' => 400,
            'message' => 'error'
        ]);
    }



    /**
     * @Route("/employee-location", name="core_employee_location", options={"expose"=true})
     * @return JsonResponse
     */
    public function getLocationByEmployeeUsingAjax()
    {
        $locations = $this->getLocationByEmployee();

        return new JsonResponse($locations);
    }


    private function getLocationByEmployee()
    {
        $user = $this->getUser();
        $returnArray=[];
        if($user->getUpozila()){
            /* @var Location $location*/
            foreach ($user->getUpozila() as $location):
                $returnArray[$location->getId()]=$location->getName();
            endforeach;
        }

        return $returnArray;
    }

    /**
     * @Route("/working-mode-select", name="working_mode_select", options={"expose"=true})
     * @return JsonResponse
     */

    public function getWorkingModeUsingAjax()
    {
        $workingModes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'settingType' => 'WORKING_MODE']);
        $returnArray=[];
        if ($workingModes){
            foreach ($workingModes as $workingMode){
                $returnArray[$workingMode->getId()]=$workingMode->getName();
            }
        }

        return new JsonResponse($returnArray);
    }



}