<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class CostBenefitAnalysisFishReportController extends AbstractController
{
    /**
     * @Route("/cost-benefit-analysis", name="cost_benefit_analysis")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @param Request $request
     */
    public function costBenefitAnalysisReport(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['reportingMonthBOM'] = $filterBy['startDate'];
            $filterBy['reportingMonthEOM'] = $filterBy['endDate'];

            unset($filterBy['startDate']);
            unset($filterBy['endDate']);
            unset($filterBy['startDateCreated']);
            unset($filterBy['endDateCreated']);
//            unset($filterBy['farmer']);

//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->getCostBenefitAnalysisReport($filterBy);
        }





        return $this->render('@TerminalbdCrm/report/costBenefitAnalysis/report-cost-benefit-analysis-less-costing-farm-poultry.html.twig', [
            'entities' => $entities,
            'filterBy' =>$filterBy,
            'searchForm' => $searchForm->createView()
        ]);

        die();


        $parentParent= $lessCostingFarm->getReportParentParent();

        if($parentParent->getSlug()=='poultry-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('report-cost-benefit-analysis-less-costing-farm-poultry.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
                'searchForm' => $searchForm->createView()
            ]);
        }
        elseif ($parentParent->getSlug()=='fish-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('report-cost-benefit-analysis-less-costing-farm-poultry.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
                'searchForm' => $searchForm->createView()
            ]);
        }else{
            return $this->render('report-cost-benefit-analysis-less-costing-farm-poultry.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
                'searchForm' => $searchForm->createView()
            ]);
        }
    }

}