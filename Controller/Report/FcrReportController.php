<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FcrReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 */
class FcrReportController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/report/fcr", name="report_fcr")
     */
    public function saleReport(Request $request): Response
    {
        $entities = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
//            $filterBy['feedType'] = $searchForm->get('feedType')->getData()->getfcrOfFeed();

//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReport($filterBy);
//            dd($entities);

        }
        return $this->render('@TerminalbdCrm/report/fcr/report-fcr.html.twig',['searchForm'=>$searchForm->createView(),'entities' => $entities]);
    }

}