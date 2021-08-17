<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class AntibioticFreeFarmReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Route("/crm/antibiotic-free-farm-report")
 */
class AntibioticFreeFarmReportController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="antibiotic_free_farm_report")
     */
    public function antibioticFreeFarm(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $medicineCost = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['startDate'] = date('Y-m', strtotime($filterBy['startDate']));

            $filterBy['reportingMonth'] = $filterBy['startDate'];

            unset($filterBy['startDateCreated']);
            unset($filterBy['endDateCreated']);
            unset($filterBy['startDate']);
            unset($filterBy['endDate']);
            unset($filterBy['farmer']);


            $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmReport($filterBy);
            $medicineCost = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmCost();
        }
        return $this->render('@TerminalbdCrm/report/antibioticFreeFarm/report-antibiotic-free-farm.html.twig',[
            'searchForm' => $searchForm->createView(),
            'filterBy' => $filterBy,
            'entities' => $entities,
            'medicineCost' => $medicineCost
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/excel", name="antibiotic_free_farm_report_excel")
     */
    public function antibioticFreeFarmExcel(Request $request)
    {

        $filterBy = $request->query->get('filterBy');
        $employeeId = $request->query->get('employeeId');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($employeeId);

        $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmReport($filterBy);
        $medicineCost = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmCost();


        $fileName = 'antibiotic_free_farm_report_' . time() . '.xls';

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/antibioticFreeFarm/report-antibiotic-free-farm-excel.html.twig',[
            'filterBy' => $filterBy,
            'entities' => $entities,
            'medicineCost' => $medicineCost
        ]);

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/pdf", name="antibiotic_free_farm_report_pdf")
     */
    public function antibioticFreeFarmPdf(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $employeeId = $request->query->get('employeeId');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($employeeId);

        $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmReport($filterBy);
        $medicineCost = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmCost();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/antibioticFreeFarm/report-antibiotic-free-farm-pdf.html.twig',[
            'filterBy' => $filterBy,
            'entities' => $entities,
            'medicineCost' => $medicineCost
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A2', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("antibiotic_free_farm_report_" . time() . ".pdf", [
            "Attachment" => false
        ]);

    }

}