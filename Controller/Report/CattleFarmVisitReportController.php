<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CattleFarmVisit;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReport;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class CattleFarmVisitReportController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/crm/cattle/farm-visit", name="cattle_farm_visit_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function cattleFarmVisitReport(Request $request)
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['employeeId'] = $filterBy['employee']->getId();
//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->getCattlefarmVisitReport($filterBy);
        }
        return $this->render('@TerminalbdCrm/report/cattle/report-cattle-farm-visit.html.twig',['searchForm' => $searchForm->createView(), 'entities' =>$entities, 'filterBy'=>$filterBy]);
    }

    /**
     * @Route("/crm/cattle/farm-visit-excel", name="cattle_farm_visit_report_excel")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     * @param Request $request
     */
    public function cattleFarmVisitReportExcel(Request $request)
    {

        $filterBy = $request->query->get('filterBy');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
        $entities = $this->getDoctrine()->getRepository(CattleFarmVisit::class)->getCattlefarmVisitReport($filterBy);

        $fileName = 'cattle_farm_visit_report_' . time() . '.xls';

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/cattle/report-cattle-farm-visit-excel.html.twig',[
            'filterBy' => $filterBy,
            'entities' => $entities
        ]);

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();
    }
    /**
     * @Route("/crm/cattle/farm-visit-pdf", name="cattle_farm_visit_report_pdf")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trainingReportPdf(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
        $entities = $this->getDoctrine()->getRepository(CattleFarmVisit::class)->getCattlefarmVisitReport($filterBy);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/cattle/report-cattle-farm-visit-pdf.html.twig',[
            'filterBy' => $filterBy,
            'entities' => $entities,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A2', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("cattle_farm_visit_report_" . time() . ".pdf", [
            "Attachment" => false
        ]);

    }
}