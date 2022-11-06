<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AgentUpgradationReport;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;
use Terminalbd\CrmBundle\Entity\ComplainDifferentProductDetails;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DailyChickPrice;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReportDetails;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;
use Terminalbd\CrmBundle\Entity\FishSalesPrice;
use Terminalbd\CrmBundle\Entity\LabService;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\TilapiaFrySales;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;
use Terminalbd\CrmBundle\Repository\AntibioticFreeFarmRepository;

/**
 * Class MonthlyReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/others", name="")
 */
class OthersReportController extends AbstractController
{
    /**
     * @Route("/", name="others_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $species = [];
        $trainingMaterials = [];
        $employee = null;
        $speciesTypesByParent=[];
        $arrFishSizes=[];
        $arrayMonth=[];

//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';

            switch ($filterBy['otherReport']){
                case 'farmer-survey-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerSurveyReport($filterBy);
                    break;

                case 'farmer-survey-cattle':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'cattle-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerSurveyReport($filterBy);
                    break;

                case 'farmer-survey-fish':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'fish-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerSurveyReport($filterBy);
                    break;

                case 'lab-service-poultry':
                    $entities = $this->getDoctrine()->getRepository(LabService::class)->getLabServiceSummaryReport($filterBy, $this->getUser());
                    break;

                case 'fcr-different-companies-poultry':
                    $entities = $this->getDoctrine()->getRepository(FcrDifferentCompanies::class)->getFcrDifferentCompaniesReport($filterBy, $this->getUser());
                    break;

                case 'company-wise-feed-sale-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('poultry', $filterBy, $this->getUser());
                    break;

                case 'company-wise-boiler-chick':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeForBoilerChick($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleReport('poultry-boiler-chicks', $filterBy, $this->getUser());
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

                case 'farmer-training-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('poultry-breed', $filterBy, $this->getUser());
                    break;

                case 'farmer-training-cattle':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'cattle-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('cattle-breed', $filterBy, $this->getUser());
                    break;

                case 'farmer-training-fish':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'fish-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('fish-breed', $filterBy, $this->getUser());
                    break;

                case 'doc-price':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getDocPriceReport($filterBy, $this->getUser());
                    break;

                case 'meat-egg-price':
                    $entities = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->getMeatEggPriceReport($filterBy, $this->getUser());
                    break;

//                case 'complain-poultry':
//                    dd('Stay for anonymous reason!!');
//                    $entities = [];
//                    break;
                case 'doc-complain':
                    $entities = $this->getDoctrine()->getRepository(ComplainDifferentProductDetails::class)->getComplainReport($filterBy, $this->getUser(), 'COMPLAIN_DOC');
                    break;
                case 'feed-complain':
                    $entities = $this->getDoctrine()->getRepository(ComplainDifferentProductDetails::class)->getComplainReport($filterBy, $this->getUser(), 'COMPLAIN_FEED');
                    break;

                case 'expense':
                    $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpenseReport($filterBy, $this->getUser());

                    break;

                case 'new-agent-upgradation-cattle':
                    $entities = $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->getAgentUpgradationReport('cattle-breed',$filterBy, $this->getUser());
                    break;

                case 'new-agent-upgradation-fish':
                    $entities = $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->getAgentUpgradationReport('fish-breed',$filterBy, $this->getUser());
                    break;

                case 'fish-sales-price':

                    $breedNameObj = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('status'=>1, 'settingType'=>'BREED_NAME','slug'=>'fish-breed'));

                    $speciesTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breedNameObj));


                    $fishSizes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FISH_SIZE'));
                    $arrFishSizes=[];
                    foreach ($fishSizes as $fishSize){
                        $arrFishSizes[$fishSize->getParent()->getId()][]=$fishSize;
                    }

                    for($i=1; $i<=12; $i++){
                        $month = date('F', mktime(0, 0, 0, $i, 10));
                        $arrayMonth[]=$month;
                    }


                    $entities = $this->getDoctrine()->getRepository(FishSalesPrice::class)->getFishSalesPriceReport( $filterBy, $this->getUser());

                    break;

                case 'fish-tilapia-fry-sales':

                    $breedNameObj = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('status'=>1, 'settingType'=>'BREED_NAME','slug'=>'fish-breed'));

                    $speciesTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breedNameObj));


                    $fishSizes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FISH_SIZE'));
                    $arrFishSizes=[];
                    foreach ($fishSizes as $fishSize){
                        $arrFishSizes[$fishSize->getParent()->getId()][]=$fishSize;
                    }

                    for($i=1; $i<=12; $i++){
                        $month = date('F', mktime(0, 0, 0, $i, 10));
                        $arrayMonth[]=$month;
                    }

                    $entities = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getTilapiaFrySalesReport( $filterBy, $this->getUser());
                    break;

                default:
                    $entities = [];
                    break;
            }
        }

        if($request->request->get('excel')){
            $html = $this->renderView('@TerminalbdCrm/report/others/excel.html.twig',[
                'entities' => $entities,
                'filterBy' => $filterBy,
                'species' => $species,
                'trainingMaterials' => $trainingMaterials,
                'employee' => $employee,
                'speciesTypes' => $speciesTypesByParent,
                'fishSizes' => $arrFishSizes,
                'arrayMonth' => $arrayMonth,
                'report' => isset($filterBy['otherReport']) &&$filterBy['otherReport']!=''?$filterBy['otherReport']:'',
            ]);

            $fileName = $filterBy['otherReport'] .'_'.time().".xls";

            header("Content-Type:  application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$fileName");

            echo $html;
            die();

        }

        return $this->render('@TerminalbdCrm/report/others/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
            'report' => isset($filterBy['otherReport']) &&$filterBy['otherReport']!=''?$filterBy['otherReport']:'',
        ]);
    }

}