<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\CattlePerformanceDetails;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcrDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class MonthlyReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/monthly", name="")
 */
class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/", name="monthly_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $species = [];
        $employee = null;
        $report = null;
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $isExcel= $request->request->get('excel');
            $report = $form->getData()['monthlyReport'];

            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';

            $employee = $form->getData()['employee'];

            switch ($report->getSlug()){
                case 'fcr-before-sale-boiler':
                case 'fcr-after-sale-boiler':
                case 'fcr-before-sale-sonali':
                case 'fcr-after-sale-sonali':
                    $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrDetailsByEmployee($report,$filterBy);
                    break;
                case 'layer-performance-brown':
                case 'layer-performance-white':
                    $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByEmployeeAndDate($report, $filterBy);
                    break;
                case 'antibiotic-free-farm-poultry':
                    $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmByEmployeeAndDate($report, $filterBy);
                    break;
                case 'less-costing-farm-poultry':
                case 'less-costing-farm-fish':
                    $entities = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->getLessCostingFarmByEmployeeAndDate($report, $filterBy);
                    break;
                case 'disease-mapping-report-poultry':
                case 'disease-mapping-report-cattle':
                case 'disease-mapping-report-fish':
                    $entities = $this->getDoctrine()->getRepository(DiseaseMapping::class)->getDiseasesMappingReportByEmployeeDate($report, $filterBy);
                    break;
                case 'farmer-touch-report-poultry':
                case 'farmer-touch-report-fish':
                case 'farmer-touch-report-cattle':
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReportByEmployeeDate($report, $filterBy);
                    break;

                case 'company-species-wise-average-fcr-before':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()){
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('BEFORE', $filterBy, $this->getUser());

                    break;

                case 'company-species-wise-average-fcr-after':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()){
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('AFTER', $filterBy, $this->getUser());

                    break;

                case 'fattening-performance-report':
                case 'dairy-performance-report':

                $entities = $this->getDoctrine()->getRepository(CattlePerformanceDetails::class)->getPerformanceReport($report, $filterBy, $this->getUser());

                    break;

                default:
                    $entities = [];
                    break;
            }

        }

        if(isset($isExcel) && !empty($isExcel)){
            $html = $this->renderView('@TerminalbdCrm/report/monthlyReport/excel.html.twig',[
                'entities' => $entities,
                'filterBy' => $filterBy,
                'lifeCycleSlug' => $report->getSlug(),
                'employee' => $employee,
                'report' => $report,
                'species' => $species,
            ]);

            $fileName = $report->getSlug().'_'.time().".xls";

            header("Content-Type:  application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$fileName");

            echo $html;
            die;

        }

        return $this->render('@TerminalbdCrm/report/monthlyReport/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'lifeCycleSlug' => $report ? $report->getSlug() : null,
            'employee' => $employee,
            'report' => $report,
            'species' => $species,
        ]);
    }

}