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

use App\Entity\Core\Agent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CattlePerformance;
use Terminalbd\CrmBundle\Entity\CattlePerformanceDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\CattleLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/cattle/performance")
 */
class CattlePerformanceController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="cattle_performance_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {

        $entity = new CattlePerformance();
        $existReport = $this->getDoctrine()->getRepository(CattlePerformance::class)->getCattlePerformanceReportByReportingDateAndFeedType($report, $crmCustomer, $this->getUser());
        if ($existReport){

            return $this->redirectToRoute('cattle_performance_details_modal', ['id'=>$existReport->getId()]);
        }

            $reportingDate = date('Y-m-d',strtotime('now'));

            $entity->setReportingMonth(new \DateTime($reportingDate));
            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');

        return $this->redirectToRoute('cattle_performance_details_modal', ['id'=>$entity->getId()]);

    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="cattle_performance_details_modal", options={"expose"=true})
     */
    public function cattlePerformanceDetailsModal( Request $request, CattlePerformance $cattlePerformance): Response
    {

        $breedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE','parent'=>$cattlePerformance->getReport()->getParent()));

        if($cattlePerformance->getReport()->getSlug()=='dairy-performance-report'){

            return $this->render('@TerminalbdCrm/cattlePerformance/dairy-performance-details-report-modal.html.twig', [
                'cattlePerformance' => $cattlePerformance,
                'breedTypes' => $breedTypes,
            ]);
        }

        return $this->render('@TerminalbdCrm/cattlePerformance/fattening-performance-details-report-modal.html.twig', [
            'cattlePerformance' => $cattlePerformance,
            'breedTypes' => $breedTypes,
        ]);
    }


    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/fattening/details/add", methods={"POST"}, name="crm_fattening_performance_detail_report_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function addFatteningPerformanceDetailsReport(Request $request, CattlePerformance $cattlePerformance): Response
    {
        $data = $request->request->all();

        $breed_type= null;
        if(isset($data['breed_type'])&&$data['breed_type']!=''){
            $breed_type = $this->getDoctrine()->getRepository(Setting::class)->find($data['breed_type']);
        }

        $entity = new CattlePerformanceDetails();
        $entity->setCrmCattlePerformance($cattlePerformance);
        $entity->setCustomer($cattlePerformance->getCustomer());
        $entity->setAgent($cattlePerformance->getCustomer()->getAgent());


        $visitingDate = isset($data['visiting_date'])&&$data['visiting_date']!=""?date('Y-m-d',strtotime($data['visiting_date'])):date('Y-m-d',strtotime('now'));

        $entity->setVisitingDate(new \DateTime($visitingDate));

        $entity->setBreedType(is_object($breed_type)?$breed_type:null);
        $entity->setAgeOfCattleMonth(isset($data['age_of_cattle_month'])&&$data['age_of_cattle_month']!=""?(float)$data['age_of_cattle_month']:0);
        $entity->setPreviousBodyWeight(isset($data['previous_body_weight'])&&$data['previous_body_weight']!=""?(float)$data['previous_body_weight']:0);
        $entity->setPresentBodyWeight(isset($data['present_body_weight'])&&$data['present_body_weight']!=""?(float)$data['present_body_weight']:0);
        $entity->setDurationOfBwtDifference(isset($data['duration_of_bwt_difference'])&&$data['duration_of_bwt_difference']!=""?(float)$data['duration_of_bwt_difference']:0);
        $entity->setConsumptionFeedIntakeReadyFeed(isset($data['consumption_feed_intake_ready_feed'])&&$data['consumption_feed_intake_ready_feed']!=""?(float)$data['consumption_feed_intake_ready_feed']:0);
        $entity->setConsumptionFeedIntakeConventional(isset($data['consumption_feed_intake_conventional'])&&$data['consumption_feed_intake_conventional']!=""?(float)$data['consumption_feed_intake_conventional']:0);
        $entity->setFodderGreenGrassKg(isset($data['fodder_green_grass_kg'])&&$data['fodder_green_grass_kg']!=""?(float)$data['fodder_green_grass_kg']:0);
        $entity->setFodderStrawKg(isset($data['fodder_straw_kg'])&&$data['fodder_straw_kg']!=""?(float)$data['fodder_straw_kg']:0);
        $entity->setNameOfReadyFeed(isset($data['name_of_ready_feed'])&&$data['name_of_ready_feed']!=""?(float)$data['name_of_ready_feed']:0);
        $entity->setRemarks(isset($data['remarks'])&&$data['remarks']!=""?(float)$data['remarks']:0);

        $entity->setBodyWeightDifference($entity->calculateBodyWeightDifference());
        $entity->setAverageWeightPerDay($entity->calculateAverageWeightPerDay());
        $entity->setAverageWeightPerKgConsumptionFeed($entity->calculateAverageWeightPerKgConsumptionFeed());
        $entity->setAverageWeightPerKgDm($entity->calculateAverageWeightPerKgDm());
        $entity->setConsumptionFeedIntakeTotal($entity->calculationConsumptionFeedIntakeTotal());
        $entity->setDmOfFodderGreenGrassKg($entity->calculateDmOfFodderGreenGrassKg());
        $entity->setDmOfFodderStrawKg($entity->calculateDmOfFodderStrawKg());
        $entity->setTotalDmKg($entity->calculateTotalDmKg());
        $entity->setDmRequirementByBwtKg($entity->calculateDmRequirementByBwtKg());


        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.created_successfully');

        return new JsonResponse(
            array(
                'success'=>'Success',
                'status'=>200,
            )
        );

    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/dairy/details/add", methods={"POST"}, name="crm_dairy_performance_detail_report_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function addDairyPerformanceDetailsReport(Request $request, CattlePerformance $cattlePerformance): Response
    {
        $data = $request->request->all();

        $breed_type= null;
        if(isset($data['breed_type'])&&$data['breed_type']!=''){
            $breed_type = $this->getDoctrine()->getRepository(Setting::class)->find($data['breed_type']);
        }

        $entity = new CattlePerformanceDetails();
        $entity->setCrmCattlePerformance($cattlePerformance);
        $entity->setCustomer($cattlePerformance->getCustomer());
        $entity->setAgent($cattlePerformance->getCustomer()->getAgent());


        $visitingDate = isset($data['visiting_date'])&&$data['visiting_date']!=""?date('Y-m-d',strtotime($data['visiting_date'])):date('Y-m-d',strtotime('now'));

        $entity->setVisitingDate(new \DateTime($visitingDate));
        $entity->setBreedType(is_object($breed_type)?$breed_type:null);
        $entity->setAgeOfCattleMonth(isset($data['age_of_cattle_month'])&&$data['age_of_cattle_month']!=""?(float)$data['age_of_cattle_month']:0);
//        $entity->setPreviousBodyWeight(isset($data['previous_body_weight'])&&$data['previous_body_weight']!=""?(float)$data['previous_body_weight']:0);
        $entity->setPresentBodyWeight(isset($data['present_body_weight'])&&$data['present_body_weight']!=""?(float)$data['present_body_weight']:0);
//        $entity->setDurationOfBwtDifference(isset($data['duration_of_bwt_difference'])&&$data['duration_of_bwt_difference']!=""?(float)$data['duration_of_bwt_difference']:0);

        $entity->setLactationNo(isset($data['lactation_no'])&&$data['lactation_no']!=""?(float)$data['lactation_no']:0);
        $entity->setAgeOfLactation(isset($data['age_of_lactation'])&&$data['age_of_lactation']!=""?(float)$data['age_of_lactation']:0);
        $entity->setAverageWeightPerDay(isset($data['average_weight_per_day'])&&$data['average_weight_per_day']!=""?(float)$data['average_weight_per_day']:0);
        $entity->setMilkFatPercentage(isset($data['milk_fat_percentage'])&&$data['milk_fat_percentage']!=""?(float)$data['milk_fat_percentage']:0);

        $entity->setConsumptionFeedIntakeReadyFeed(isset($data['consumption_feed_intake_ready_feed'])&&$data['consumption_feed_intake_ready_feed']!=""?(float)$data['consumption_feed_intake_ready_feed']:0);
        $entity->setConsumptionFeedIntakeConventional(isset($data['consumption_feed_intake_conventional'])&&$data['consumption_feed_intake_conventional']!=""?(float)$data['consumption_feed_intake_conventional']:0);
        $entity->setFodderGreenGrassKg(isset($data['fodder_green_grass_kg'])&&$data['fodder_green_grass_kg']!=""?(float)$data['fodder_green_grass_kg']:0);
        $entity->setFodderStrawKg(isset($data['fodder_straw_kg'])&&$data['fodder_straw_kg']!=""?(float)$data['fodder_straw_kg']:0);
        $entity->setNameOfReadyFeed(isset($data['name_of_ready_feed'])&&$data['name_of_ready_feed']!=""?(float)$data['name_of_ready_feed']:0);
        $entity->setRemarks(isset($data['remarks'])&&$data['remarks']!=""?(float)$data['remarks']:0);

        $entity->setAverageWeightPerKgConsumptionFeed($entity->calculateAverageWeightPerKgConsumptionFeed());
        $entity->setAverageWeightPerKgDm($entity->calculateAverageWeightPerKgDm());
        $entity->setConsumptionFeedIntakeTotal($entity->calculationConsumptionFeedIntakeTotal());
        $entity->setDmOfFodderGreenGrassKg($entity->calculateDmOfFodderGreenGrassKg());
        $entity->setDmOfFodderStrawKg($entity->calculateDmOfFodderStrawKg());
        $entity->setTotalDmKg($entity->calculateTotalDmKg());
        $entity->setDmRequirementByBwtKg($entity->calculateDmRequirementByBwtKgForDairy());

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.created_successfully');

        return new JsonResponse(
            array(
                'success'=>'Success',
                'status'=>200,
            )
        );

    }

    /**
     * @param CattlePerformance cattlePerformance
     * @Route("/detail/{id}/refresh", methods={"GET"}, name="crm_cattle_performance_detail_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function cattleLifeCycleReportRefresh(CattlePerformance $cattlePerformance): Response
    {
        if($cattlePerformance->getReport()->getSlug()=='dairy-performance-report') {
            return $this->render('@TerminalbdCrm/cattlePerformance/partial/dairy-performance.html.twig', [
                'cattlePerformance' => $cattlePerformance,
            ]);
        }
        return $this->render('@TerminalbdCrm/cattlePerformance/partial/fattening-performance.html.twig', [
            'cattlePerformance' => $cattlePerformance,
        ]);
    }
    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="cattle_performance_detail_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CattlePerformanceDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    /**
     * @param $report
     * @Route("/life/cycle/{report}", methods={"GET"}, name="crm_cattle_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report): Response
    {

        $entities = $this->getDoctrine()->getRepository(CattleLifeCycle::class)->getCattleLifeCycleByReportType($report);
        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report.html.twig',['entities' => $entities]);
    }

    /**
     * @param CattleLifeCycle $cattleLifeCycle
     * @Route("/life/cycle/{id}/report", methods={"GET"}, name="crm_cattle_report_detail")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportDetails( CattleLifeCycle $cattleLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report-details.html.twig',['cattleLifeCycle' => $cattleLifeCycle]);
    }


}
