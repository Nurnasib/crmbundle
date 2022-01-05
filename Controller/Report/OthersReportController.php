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
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;
use Terminalbd\CrmBundle\Repository\AntibioticFreeFarmRepository;

/**
 * Class MonthlyReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
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

        $employee = null;
        $report = null;
        $form = $this->createForm(SearchFilterFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()){

            $isExcel= $request->request->get('excel');

            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['report'] = $form->getData()['otherReport'];

            $employee = $form->getData()['employee'];

            switch ($filterBy['report']){
                case 'farmer-survey-poultry':
                    $breed = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'slug' => 'poultry-breed', 'settingType' => 'BREED_NAME']);
                    $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerSurveyReport($filterBy);
                    break;
                default:
                    $entities = [];
                    break;
            }

        }

        if($request->request->get('excel')){
            $html = $this->renderView('@TerminalbdCrm/report/others/excel.html.twig',[
                'entities' => $entities,
                'filterBy'=> $filterBy,
                'species'=> $species,
                'employee'=> $employee,
            ]);

            $fileName = $filterBy['report'] .'_'.time().".xls";

            header("Content-Type:  application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$fileName");

            echo $html;
            die();

        }

        return $this->render('@TerminalbdCrm/report/others/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy'=> $filterBy,
            'species'=> $species,
            'employee'=> $employee,
        ]);
    }

}