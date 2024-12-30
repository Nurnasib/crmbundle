<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
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
use Terminalbd\CrmBundle\Entity\Challenger;
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;
use Terminalbd\CrmBundle\Entity\ComplainDifferentProductDetails;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcrDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormForSalesAndMarketingType;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class MonthlyReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/sales-marketing", name="")
 */
class SalesAndMarketingReportController extends AbstractController
{
    /**
     * @Route("/{report}", name="sales_marketing_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, $report, ParameterBagInterface $parameterBag)
    {

        $filterBy = [];
        $entities = [];
        $species = [];
        $employee = null;
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormForSalesAndMarketingType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $filterBy = $form->getData();
//            $report = $form->getData()['monthlyReport'];

            $filterBy['startMonth'] = $form->getData()['startMonth']? $form->getData()['startMonth']->format('Y-m-d'):null;
            $filterBy['endMonth'] = $form->getData()['endMonth']?$form->getData()['endMonth']->format('Y-m-t'):null;
            $filterBy['otherReport'] = $report;
            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['poultryFramType'] = $form->getData()['poultryFramType'];
            $filterBy['chickType'] = $form->getData()['chickType'];
            $filterBy['meatEggBreedType'] = $form->getData()['meatEggBreedType'];
//            $filterBy['employee'] = $form->getData()['employee'] ? $form->getData()['employee'] : '';
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['region'] = $form->getData()['region'] ? $form->getData()['region']->getId() : '';
            $filterBy['hatchery'] = $form->getData()['hatchery'] ? $form->getData()['hatchery']->getId() : '';

            $employee = $form->getData()['employee'];

            switch ($report) {
                case 'challenges-problem':
                case 'challenges-idea':
                case 'competitors-activity':
                    $entities = $this->getDoctrine()->getRepository(Challenger::class)->getChallengerByEmployee($report, $filterBy, $this->getUser());
                    break;
                    
                case 'doc-price-monthly':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getDocPriceMonthly($filterBy, $this->getUser());
                    break;
                
                case 'doc-price-daily':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getDailyDocPriceReport($filterBy, $this->getUser());
                    break;


                case 'company-wise-doc-price-monthly':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getCompanyWiseAvgDocPriceMonthly($filterBy, $this->getUser());
                    break;

                case 'company-wise-doc-price-daily':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getCompanyWiseAvgDailyDocPrice($filterBy, $this->getUser());
                    break;

                case 'meat-egg-price-monthly':
                    $entities = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->getMeatEggPriceReport($filterBy, $this->getUser());
                    break;

                case 'meat-egg-price-daily':
                    $entities = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->getDailyMeatEggPrice($filterBy, $this->getUser());
                    break;
                    
                case 'doc-complain':
                    $entities = $this->getDoctrine()->getRepository(ComplainDifferentProductDetails::class)->getComplainReport($filterBy, $this->getUser(), 'COMPLAIN_DOC');
                    break;
                    
                case 'feed-complain':
                    $entities = $this->getDoctrine()->getRepository(ComplainDifferentProductDetails::class)->getComplainReport($filterBy, $this->getUser(), 'COMPLAIN_FEED');
                    break;

                case 'company-wise-feed-sale-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('poultry', $filterBy, $this->getUser());
                    break;

                case 'company-wise-layer-chick':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeForLayerChick($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('poultry-layer-chicks', $filterBy, $this->getUser());
                    break;
                    
                case 'company-wise-feed-sale-cattle':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'cattle-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('cattle', $filterBy, $this->getUser());
                    break;

                case 'company-wise-feed-sale-fish':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'fish-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('fish', $filterBy, $this->getUser());
                    break;

                default:
                    $entities = [];
                    break;
            }
        }
        return $this->render('@TerminalbdCrm/report/salesAndMarketing/index.html.twig', [
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'employee' => $employee,
            'report' => $report,
            'reportSlug' => $report,
            'species' => $species,
            ]
        );
    }
}