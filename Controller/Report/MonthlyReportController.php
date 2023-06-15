<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
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
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/monthly", name="")
 */
class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/{reportSlug}", name="monthly_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ParameterBagInterface $parameterBag, ManagerRegistry $doctrine, $reportSlug)
    {
        $filterBy = [];
        $entities = [];
        $species = [];
        $employee = null;
        $report = null;
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);

        $report = $doctrine->getRepository(Setting::class)->findOneBy(['settingType'=>'FARMER_REPORT','slug'=>$reportSlug, 'status'=>1]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
//            $report = $form->getData()['monthlyReport'];
            
            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['feedMill'] = $form->getData()['feedMill'] ? $form->getData()['feedMill']->getId() : '';
            $filterBy['region'] = $form->getData()['region'] ? $form->getData()['region']->getId() : '';
            $filterBy['feedCompany'] = isset($form->getData()['feedCompany']) ? $form->getData()['feedCompany'] : '';
            $filterBy['feedType'] = isset($form->getData()['feedType']) ? $form->getData()['feedType']->getId() : '';

            $employee = $form->getData()['employee'];

            switch ($report->getSlug()) {
                case 'fcr-before-sale-boiler':
                case 'fcr-after-sale-boiler':
                case 'fcr-before-sale-sonali':
                case 'fcr-after-sale-sonali':
                    $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrDetailsByEmployee($report, $filterBy, $this->getUser());
                    break;
                case 'layer-performance-brown':
                case 'layer-performance-white':
                    $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByEmployeeAndDate($report, $filterBy, $this->getUser());
                    break;
                case 'antibiotic-free-farm-poultry':
                    $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmByEmployeeAndDate($report, $filterBy, $this->getUser());
                    break;
                case 'less-costing-farm-poultry':
                case 'less-costing-farm-fish':
                    $entities = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->getLessCostingFarmByEmployeeAndDate($report, $filterBy, $this->getUser());
                    break;
                case 'disease-mapping-report-poultry':
                case 'disease-mapping-report-cattle':
                case 'disease-mapping-report-fish':
                    $entities = $this->getDoctrine()->getRepository(DiseaseMapping::class)->getDiseasesMappingReportByEmployeeDate($report, $filterBy, $this->getUser());
                    break;
                case 'farmer-introduce-report-poultry':
                case 'farmer-introduce-report-fish':
                case 'farmer-introduce-report-cattle':
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReportByEmployeeDate($report, $filterBy);
                    break;

                case 'company-species-wise-average-fcr-before':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()) {
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('BEFORE', $filterBy, $this->getUser());

                    break;

                case 'company-species-wise-average-fcr-after':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()) {
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('AFTER', $filterBy, $this->getUser());

                    break;

                case 'fattening-performance-report':
                case 'dairy-performance-report':
                    $entities = $this->getDoctrine()->getRepository(CattlePerformanceDetails::class)->getPerformanceReport($report, $filterBy, $this->getUser());

                    break;

                case 'cattle-farm-visit-report':
                    $entities = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->getCattleFarmVisitReport($report, $filterBy, $this->getUser());
                    break;

                default:
                    $entities = [];
                    break;
            }
        }
        return $this->render('@TerminalbdCrm/report/monthlyReport/index.html.twig', ['form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'reportSlug' => $report ? $report->getSlug() : $reportSlug,
            'employee' => $employee,
            'report' => $report,
            'species' => $species,]);
    }
}