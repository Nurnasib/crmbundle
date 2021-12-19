<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class LayerLifeCycleReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class LayerLifeCycleReportController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/crm/layer/life-cycle/report", name="layer_life_cycle_report")
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
     * @Route("/crm/layer/life-cycle/report/excel", name="layer_life_cycle_excel")
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


    /**
     * @Route("/crm/layer/report/pdf", methods={"GET"}, name="crm_layer_life_cycle_report_pdf")
     * @param Request $request
     * @return Response
     */
    public function reportPdf(Request $request): Response
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['farmer'] = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($filterBy['farmerId']);
//        dd($filterBy);

        $entities = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->getLayerLifeCycleReport($filterBy);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/layer/report-life-cycle-pdf.html.twig',['entities' => $entities]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("layer-life-cycle-report.pdf", [
            "Attachment" => false
        ]);
    }

}