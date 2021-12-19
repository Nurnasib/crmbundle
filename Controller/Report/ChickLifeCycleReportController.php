<?php


namespace Terminalbd\CrmBundle\Controller\Report;

use Dompdf\Dompdf;
use Dompdf\Options;
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


/**
 * Class ChickLifeCycleReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Route("/crm/chick/life-cycle")
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class ChickLifeCycleReportController extends AbstractController
{
    /**
     * @param string $breed
     * @param Request $request
     * @return Response
     * @Route("/{breed}", methods={"GET","POST"}, name="chick_life_cycle_report")
     */
    public function indexReport( string $breed, Request $request): Response
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class)->remove('farmer')->remove('startDateCreated')->remove('endDateCreated');
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['breed'] = $breed;
            $filterBy['employeeId'] = $searchForm->get('employee')->getData() ? $searchForm->get('employee')->getData()->getId() : null;

            $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/chick/report-life-cycle.html.twig',[
            'searchForm' => $searchForm->createView(),
            'entities' => $entities,
            'filterBy'=>$filterBy,
            'breed' => $breed
        ]);
    }

    /**
     * @param Request $request
     * @return void
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


    /**
     * @Route("/crm/chick/report/pdf", methods={"GET"}, name="crm_chick_report_pdf")
     * @param Request $request
     * @return Response
     */
    public function reportPdf(Request $request): Response
    {
        $filterBy = $request->query->get('filterBy');

        $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($filterBy['farmerId']);
        $filterBy['farmer'] = $farmer;

        $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($filterBy);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/chick/report-life-cycle-pdf.html.twig',['entities' => $entities]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($filterBy['slug'] . ".pdf", [
            "Attachment" => false
        ]);
    }

}