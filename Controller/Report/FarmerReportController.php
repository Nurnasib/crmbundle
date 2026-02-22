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
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getLineManagerEmployee( $filterBy );
            //dd($filterBy);

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
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $loggedUser,'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getLineManagerEmployee( $filterBy );
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
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $filterBy = $form->getData();

            $employee = $form->getData()['employee'];

            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['loggedUser'] = $loggedUser;

            $entities = $userRepo->getEmployeeReport( $filterBy );
            //dd($entities);

        }

        //$speciesTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesTypeByParentSlug('poultry-breed');

        return $this->render('@TerminalbdCrm/report/farmerReport/employee_farmer_index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'species' => $species,
            'trainingMaterials' => $trainingMaterials,
            'employee'=> $employee,
            //'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'arrayMonth' => $arrayMonth,
        ]);
    }

}