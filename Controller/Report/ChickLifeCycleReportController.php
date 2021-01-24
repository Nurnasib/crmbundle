<?php


namespace Terminalbd\CrmBundle\Controller\Report;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;


class ChickLifeCycleReportController extends AbstractController
{
    /**
     * @param $report
     * @Route("/crm/chick/{report}", methods={"GET","POST"}, name="crm_chick_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report, Request $request): Response
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class)->remove('employee')->remove('startDateCreated')->remove('endDateCreated');
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['slug'] = $report;
            $filterBy['farmerId'] = $searchForm->get('farmer')->getData()->getId();

            $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/chick/report-life-cycle.html.twig',['searchForm' => $searchForm->createView(), 'entities' => $entities, 'filterBy'=>$filterBy]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/excel", name="chick_excel")
     */
    public function reportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');

        $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($filterBy['farmerId']);
        $filterBy['farmer'] = $farmer;

        $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($filterBy);

        $fileName = $filterBy['slug'].'_'.time().'.xls';

        $html = $this->renderView('@TerminalbdCrm/report/chick/report-life-cycle-excel.html.twig', ['entities'=>$entities]);

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();

    }

}