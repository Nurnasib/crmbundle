<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class LayerLifeCycleReportController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/report/layer-life-cycle", name="layer_lfe_cycle")
     */
    public function lifeCycleReport(Request $request)
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['farmerId'] = $searchForm->get('farmer')->getData()->getId();
//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->getLayerLifeCycleReport($filterBy);
//            dd($entities);
        }

        return $this->render('@TerminalbdCrm/report/layer/report-life-cycle.html.twig', ['searchForm'=>$searchForm->createView(), 'entities'=>$entities, 'filterBy'=>$filterBy]);
    }

    /**
     * @param Request $request
     * @Route("/report/layer-life-cycle-excel", name="layer_life_cycle_excel")
     */
    public function reportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['farmer'] = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($filterBy['farmerId']);

        $entities = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->getLayerLifeCycleReport($filterBy);

        $html = $this->renderView('@TerminalbdCrm/report/layer/report-life-cycle-excel.html.twig', ['entities'=>$entities]);

        $fileName = 'layer-life-cycle-report'.'_'.time().'.xls';

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachement; filename=$fileName");

        echo $html;
        die();
    }

}