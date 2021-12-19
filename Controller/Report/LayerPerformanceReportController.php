<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class LayerPerformanceReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class LayerPerformanceReportController extends AbstractController
{
    /**
     * @Route("/crm/layer/performance/report", methods={"GET","POST"}, name="layer_performance_report")
     * @param Request $request
     * @return Response
     */
    public function indexReport(Request $request): Response
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class)->remove('farmer')->remove('startDate')->remove('endDate');

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['employeeId'] = $searchForm->get('employee')->getData()->getId();
            $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReport($filterBy);
        }

        return $this->render('@TerminalbdCrm/report/layer/report-performance.html.twig',['searchForm' => $searchForm->createView(), 'entities' => $entities, 'filterBy' => $filterBy]);
    }

    /**
     * @param Request $request
     * @Route("/layer_performance/report/excel", name="layer_performance_report_excel")
     */
    public function reportExcel(Request $request)
    {
        $filteBy = $request->query->get('filterBy');
        $filteBy['employe'] = $this->getDoctrine()->getRepository(User::class)->find($filteBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReport($filteBy);

        $fileName = 'layer_performance_report'.'_'.time().'.xls';
        $html = $this->renderView('@TerminalbdCrm/report/layer/report-performance-excel.html.twig',['entities'=>$entities]);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachement; filename=$fileName");

        echo $html;
        die();
    }


    /**
     * @Route("/crm/layer-performance/report/pdf", methods={"GET"}, name="crm_layer_performance_report_pdf")
     * @param Request $request
     * @return Response
     */
    public function reportPdf(Request $request): Response
    {
        $filteBy = $request->query->get('filterBy');
        $filteBy['employe'] = $this->getDoctrine()->getRepository(User::class)->find($filteBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReport($filteBy);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/layer/report-performance-pdf.html.twig',['entities' => $entities]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("layer-performance-report.pdf", [
            "Attachment" => false
        ]);
    }

}