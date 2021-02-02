<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CattleFarmVisit;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class CattleFarmVisitReportController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/crm/cattle/farm-visit", name="cattle_farm_visit_report")
     */
    public function cattleFarmVisitReport(Request $request)
    {
        $entties = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
//            dd($filterBy);
            $entties = $this->getDoctrine()->getRepository(CattleFarmVisit::class)->getCattlefarmVisitReport($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/cattle/report-cattle-farm-visit.html.twig',['searchForm' => $searchForm->createView(), 'entities' =>$entties]);
    }
}