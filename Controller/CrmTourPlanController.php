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
use Terminalbd\CrmBundle\Form\CrmTourPlanFormType;
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
        $requestDate = $request->query->get('date')?date('Y-m', strtotime($request->query->get('date'))):date('Y-m');
        $entities = $this->getDoctrine()->getRepository(CrmVisitPlan::class)->getMonthlyTourPlanByEmployeeAndDate($this->getUser()->getId(), $requestDate, 'monthly');
//        dd($entities);
        return $this->render('@TerminalbdCrm/crmTourPlan/index.html.twig', [
            'entities' => $entities,
            'requestDate' => $requestDate
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
                $existings = $this->getDoctrine()->getRepository(CrmVisitPlan::class)->findOneBy(['visitDate'=>new \DateTime( date('Y-m', strtotime($date['visitDate'])).'-'.$i), 'employee'=>$this->getUser()]);
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



}