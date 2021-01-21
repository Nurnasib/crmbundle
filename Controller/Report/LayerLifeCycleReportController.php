<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->getLayerLifeCycleReport($filterBy);
//            dd($entities);
        }

        return $this->render('@TerminalbdCrm/report/layer/report-life-cycle.html.twig', ['searchForm'=>$searchForm->createView(), 'entities'=>$entities]);
    }

}