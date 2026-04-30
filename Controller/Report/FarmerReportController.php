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
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_LINE_MANAGER') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/farmars", name="")
 */
class FarmerReportController extends AbstractController
{
    /**
     * @Route("/summary", name="farmar_summary_report")
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

            $entities = $userRepo->getRegionalHeadEmployee( $filterBy );
            
        }

        return $this->render('@TerminalbdCrm/report/farmerReport/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }

    /**
     * @Route("/details", name="farmar_details_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function details(Request $request)
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

            $entities = $userRepo->getRegionalHeadEmployee( $filterBy );

        }

        $speciesTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');

        return $this->render('@TerminalbdCrm/report/farmerReport/details.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }

    /**
     * @Route("/region_report", name="farmer_region_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function regionReport(Request $request)
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
        $loggedUser = $this->getUser();
        //$loggedUserDesignation = $loggedUser?->getDesignation()?->getName() ?? 'N/A';
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getLineManagerEmployeeRegionReport( $filterBy );
            //dd($entities);

        }
        return $this->render('@TerminalbdCrm/report/farmerReport/region_wise_index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/national_total_report", name="farmer_national_total_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function NationalTotalReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getLineManagerEmployeeNationalTotal( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/national_total_index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_report", name="employee_farmer_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getEmployeeReport( $filterBy );

            //dd($entities[6] ?? null);

        }
        $speciesTypes = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeOfPoultryCattleFish();
        $filterableSpeciesType = [];
        //dd($filterBy['type']);
        if (isset($filterBy['type'])) {
            $filterableSpeciesType = [[
                'id' => $filterBy['type']->getId(),
                'name' => $filterBy['type']->getName()
            ]];
        }
        //dd($filterableSpeciesType);

//        $speciesTypesOfPoultry = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');
//        $speciesTypesOfCattle = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('cattle-breed');
//        $speciesTypesOfFish = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('fish-breed');

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_farmer_index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
//            'speciesPoultry' => $speciesTypesOfPoultry,
//            'speciesCattle' => $speciesTypesOfCattle,
//            'speciesFish' => $speciesTypesOfFish,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/single_employee_report", name="single_employee_farmer_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function singleEmployeeReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getSingleEmployeeReport( $filterBy );

            //dd($entities[6] ?? null);

        }
        $speciesTypes = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeOfPoultryCattleFish();
        $filterableSpeciesType = [];
        //dd($filterBy['type']);
        if (isset($filterBy['type'])) {
            $filterableSpeciesType = [[
                'id' => $filterBy['type']->getId(),
                'name' => $filterBy['type']->getName()
            ]];
        }
        //dd($filterableSpeciesType);

//        $speciesTypesOfPoultry = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');
//        $speciesTypesOfCattle = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('cattle-breed');
//        $speciesTypesOfFish = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('fish-breed');

        return $this->render('@TerminalbdCrm/report/farmerReport/single_employee_farmer_index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
//            'speciesPoultry' => $speciesTypesOfPoultry,
//            'speciesCattle' => $speciesTypesOfCattle,
//            'speciesFish' => $speciesTypesOfFish,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/new_farm_capacity_report", name="new_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getNewFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/new_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/new_farm_report", name="new_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getNewFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/new_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/daily_new_farm_capacity_report", name="daily_new_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dailyNewFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/daily_new_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/daily_new_farm_report", name="daily_new_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dailyNewFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/daily_new_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_farm_capacity_report", name="employee_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getNewFarmCapacity( $filterBy );
//            dd($entities[17]);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_farm_report", name="employee_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getNewFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_daily_farm_capacity_report", name="employee_daily_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeDailyFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_daily_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_daily_farm_report", name="employee_daily_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeDailyFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_daily_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/summery_farm_report", name="summery_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summeryFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        $species = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeIds();
        $speciesIds = array_map('intval', array_column($species, 'id'));

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['start_month'] = 1;
            $filterBy['end_month'] = 12;

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getSummeryFarm( $filterBy, $speciesIds );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/summery_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/closed_farm_capacity_report", name="closed_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closedFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getClosedFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/closed_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/closed_farm_report", name="closed_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closedFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getClosedFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/closed_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/daily_closed_farm_capacity_report", name="daily_closed_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dailyClosedFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyClosedFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/daily_new_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/daily_closed_farm_report", name="daily_closed_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dailyClosedFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyClosedFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/daily_new_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_closed_farm_capacity_report", name="employee_closed_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeClosedFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getClosedFarmCapacity( $filterBy );
//            dd($entities[17]);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/employee_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_closed_farm_report", name="employee_closed_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeClosedFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only','start_end_month_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getClosedFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/employee_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_daily_closed_farm_capacity_report", name="employee_daily_closed_farm_capacity_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeDailyClosedFarmCapacityReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyClosedFarmCapacity( $filterBy );
            //dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/employee_daily_farm_capacity.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/employee_daily_closed_farm_report", name="employee_daily_closed_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeeDailyClosedFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getDailyClosedFarmCapacity( $filterBy );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/employee_daily_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/summery_closed_farm_report", name="summery_closed_farm_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summeryClosedFarmReport(Request $request)
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
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        $species = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeIds();
        $speciesIds = array_map('intval', array_column($species, 'id'));

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['start_month'] = 1;
            $filterBy['end_month'] = 12;

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getSummeryClosedFarm( $filterBy, $speciesIds );
//            dd($entities);

        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/summery_farm.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/closed_farm_info_report", name="closed_farm_info_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closedFarmsInfoReport(Request $request)
    {
        $gg = $request->get('status');
        $filterBy = [];
        $entities = [];
        $species = [];
        $trainingMaterials = [];
        $employee = null;
        $speciesTypesByParent=[];
        $arrFishSizes=[];
        $arrayMonth=[];

//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['month_only', 'year_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;
            if ($gg)$filterBy['status'] = $gg;

            $entities = $userRepo->getClosedFarmsInfo( $filterBy );

//            dd($entities[6] ?? null);

        }
        $speciesTypes = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeOfPoultryCattleFish();
        $filterableSpeciesType = [];
        //dd($filterBy['type']);
        if (isset($filterBy['type'])) {
            $filterableSpeciesType = [[
                'id' => $filterBy['type']->getId(),
                'name' => $filterBy['type']->getName()
            ]];
        }

        return $this->render('@TerminalbdCrm/report/farmerReport/closedFarm/closed_farm_info.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/farm_visit_status_report", name="farm_visit_status_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function farmVisitStatusReport(Request $request)
    {
        $gg = $request->get('status');
        $visit = $request->get('visit');
        $filterBy = [];
        $entities = [];
        $species = [];
        $trainingMaterials = [];
        $employee = null;
        $speciesTypesByParent=[];
        $arrFishSizes=[];
        $arrayMonth=[];

//        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['start_end_date_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;
            if ($gg)$filterBy['status'] = $gg;
            if ($visit)$filterBy['visit'] = $visit;

            $entities = $userRepo->getFarmVisitsInfo( $filterBy );

//            dd($entities[5] ?? null);

        }
        $speciesTypes = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeOfPoultryCattleFish();
        $filterableSpeciesType = [];
        //dd($filterBy['type']);
        if (isset($filterBy['type'])) {
            $filterableSpeciesType = [[
                'id' => $filterBy['type']->getId(),
                'name' => $filterBy['type']->getName()
            ]];
        }

        return $this->render('@TerminalbdCrm/report/farmerReport/farm_visit_status.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/new_farm_customer_report", name="new_farm_customer_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFarmCustomer(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $af = $request->get('agent_focus');
        $species = [];
        $trainingMaterials = [];
        $employee = null;
        $speciesTypesByParent=[];
        $arrFishSizes=[];
        $arrayMonth=[];

        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only', 'farm_type_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;
            if ($af)$filterBy['agent_focus'] = $af;

            $entities = $userRepo->getNewFarmCustomerReport( $filterBy );
        }
        $speciesTypes = [];
        $filterableSpeciesType = [];
        if (isset($filterBy['farm_type'])) {
            if ($filterBy['farm_type']=='poultry-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');
            if ($filterBy['farm_type']=='cattle-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('cattle-breed');
            if ($filterBy['farm_type']=='fish-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('fish-breed');
        }

        return $this->render('@TerminalbdCrm/report/farmerReport/new_farm_customer_report.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }
    /**
     * @Route("/new_farm_introduce_report", name="new_farm_introduce_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function NewFarmerIntroduce(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $if = $request->get('intro_focus');
        $species = [];
        $trainingMaterials = [];
        $employee = null;
        $speciesTypesByParent=[];
        $arrFishSizes=[];
        $arrayMonth=[];

        $loggedUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, [
            'validation_groups' => ['year_only', 'month_only', 'farm_type_only'],
            'loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;
            if ($if)$filterBy['intro_focus'] = $if;

            $entities = $userRepo->getNewFarmCustomerReport( $filterBy );
        }
        $speciesTypes = [];
        $filterableSpeciesType = [];
        if (isset($filterBy['farm_type'])) {
            if ($filterBy['farm_type']=='poultry-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');
            if ($filterBy['farm_type']=='cattle-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('cattle-breed');
            if ($filterBy['farm_type']=='fish-breed') $filterableSpeciesType = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('fish-breed');
        }

//        dd($entities[17]);

        return $this->render('@TerminalbdCrm/report/farmerReport/new_farm_introduce_report.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'speciesTypes' => $speciesTypes,
            'filterableSpeciesType' => $filterableSpeciesType,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }


}