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
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DailyChickPrice;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReportDetails;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;
use Terminalbd\CrmBundle\Entity\LabService;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
use Terminalbd\CrmBundle\Entity\Setting;
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

        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            switch ($filterBy['otherReport']){
                case 'farmer-survey-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerSurveyReport($filterBy);
                    break;

                case 'lab-service-poultry':
                    $entities = $this->getDoctrine()->getRepository(LabService::class)->getLabServiceSummaryReport($filterBy);
                    break;

                case 'fcr-different-companies-poultry':
                    $entities = $this->getDoctrine()->getRepository(FcrDifferentCompanies::class)->getFcrDifferentCompaniesReport($filterBy);
                    break;

                case 'company-wise-feed-sale-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSale('poultry', $filterBy);
                    break;

                case 'company-wise-feed-sale-cattle':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'cattle-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSale('cattle', $filterBy);
                    break;

                case 'company-wise-feed-sale-fish':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'fish-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->getProductType($breed);
                    $entities = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSale('fish', $filterBy);
                    break;

                case 'farmer-training-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('poultry-breed', $filterBy);
                    break;

                case 'farmer-training-cattle':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'cattle-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('cattle-breed', $filterBy);
                    break;

                case 'farmer-training-fish':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'fish-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'SPECIES_TYPE','parent' => $breed));
                    $trainingMaterials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status' => 1,'settingType' => 'TRAINING_MATERIAL','parent' => $breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->getFarmerTrainingReport('fish-breed', $filterBy);
                    break;

                case 'doc-price':
                    $entities = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->getDocPriceReport($filterBy, $this->getUser());
                    break;

                case 'meat-egg-price':
                    $entities = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->getMeatEggPriceReport($filterBy, $this->getUser());
                    break;

                case 'complain-poultry':
                    dd('Stay for anonymous reason!!');
                    $entities = [];

                    break;

                case 'expense':
                    $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpense($filterBy, $this->getUser());

                    break;

                case 'new-agent-upgradation-cattle':
                    $entities = $this->getDoctrine()->getRepository(Expense::class)->getExpense($filterBy, $this->getUser());

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
        ]);
    }

}