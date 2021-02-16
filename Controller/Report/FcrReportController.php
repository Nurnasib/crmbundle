<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FcrReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 */
class FcrReportController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/crm/chick/fcr/{slug}", methods={"GET","POST"}, name="chick_fcr_report")
     */
    public function saleReport(Request $request, string $slug): Response
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['employeeId'] = $searchForm->get('employee')->getData()->getId();
            $filterBy['slug'] = $slug;

//            dd($filterBy);

            $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReport($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/fcr/report-fcr.html.twig',['searchForm'=>$searchForm->createView(),'entities' => $entities,'filterBy'=>$filterBy]);
    }

    /**
     * @param Request $request
     * @Route("/crm/chick/report/fcr/excel", name="fcr_report_excel")
     */
    public function reportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReport($filterBy);

        $fileName = 'fcr_report'.'_'.time().'.xls';
        $html = $this->renderView('@TerminalbdCrm/report/fcr/report-fcr-excel.html.twig', ['entities'=>$entities]);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachement; filename = $fileName");

        echo $html;
        die();
    }


    /**
     * @Route("/crm/chick/report/fcr/pdf", methods={"GET"}, name="crm_chick_report_fcr_pdf")
     */
    public function reportPdf(Request $request): Response
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReport($filterBy);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/fcr/report-fcr-pdf.html.twig',['entities' => $entities]);

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