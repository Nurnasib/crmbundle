<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
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
     * @Route("/crm/fcr/report", name="fcr_report")
     */
    public function saleReport(Request $request): Response
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['employeeId'] = $searchForm->get('employee')->getData()->getId();

            $entities = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReport($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/fcr/report-fcr.html.twig',['searchForm'=>$searchForm->createView(),'entities' => $entities,'filterBy'=>$filterBy]);
    }

    /**
     * @param Request $request
     * @Route("/report/fcr/excel", name="fcr_report_excel")
     */
    public function reportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReport($filterBy);

        $fileName = 'fcr_report'.'_'.time().'.xls';
        $html = $this->renderView('@TerminalbdCrm/report/fcr/report-fcr-excel.html.twig', ['entities'=>$entities]);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachement; filename = $fileName");

        echo $html;
        die();
    }

}