<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class FarmerTouchReportController extends AbstractController
{
    /**
     * @Route("/crm/farmer/{type}/{slug}", methods={"GET","POST"}, name="farmer-touch-report")
     * @param $slug
     * @param $type
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function touchReport($slug, $type, Request $request)
    {
        $breedType = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('settingType'=>'BREED_NAME','slug'=>$type));

        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['slug'] = $slug;
            $filterBy['employeeId'] = $filterBy['employee']->getId();
            $entities = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFarmertouchReport($filterBy);
//            dd($entities);
        }
        $species = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesForFarmerTouchReport($breedType->getId());
//        dd($species);

        if ($slug == 'farmer-touch-report-poultry'){
            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-poultry.html.twig',['searchForm' =>$searchForm->createView(), 'entities' =>$entities, 'filterBy'=>$filterBy, 'species'=> $species]);

        }elseif ($slug == 'farmer-touch-report-fish'){
            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-fish.html.twig',['searchForm' =>$searchForm->createView(), 'entities' =>$entities, 'filterBy'=>$filterBy, 'species'=> $species]);

        }else{
            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-cattle.html.twig',['searchForm' =>$searchForm->createView(), 'entities' =>$entities, 'filterBy'=>$filterBy, 'species'=>$species]);

        }
    }

    /**
     * @Route("/crm/farmer-touch-report/pdf", name="farmer_touch_report_pdf")
     * @param Request $request
     */
    public function touchReportPdf(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
//        dd($filterBy);
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
//        dd($filterBy);
        $entities = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFarmertouchReport($filterBy);


        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        if ($filterBy['slug'] == 'farmer-touch-report-poultry'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-poultry-pdf.html.twig',['entities' => $entities, 'species'=>$species]);
//            $html="Hello";

        }elseif ($filterBy['slug'] == 'farmer-touch-report-fish'){
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-fish-pdf.html.twig',['entities' => $entities, 'species'=>$species, 'filterBy'=> $filterBy]);
//            $html = 'Hello';
        }else{
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-cattle-pdf.html.twig',['entities' => $entities, 'species'=>$species, 'filterBy'=> $filterBy]);
        }

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($filterBy['slug'] . "_" . time() . ".pdf", [
            "Attachment" => false
        ]);

    }

    /**
     * @param Request $request
     * @Route("/crm/farmer-touch-report/excel", name="farmer_touch_report_excel")
     */
    public function touchReportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
//        dd($filterBy);
        $entities = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFarmertouchReport($filterBy);

        $fileName = $filterBy['slug'].'_'.time().'.xls';


        if ($filterBy['slug'] == 'farmer-touch-report-poultry'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-poultry-excel.html.twig', ['entities'=>$entities, 'species'=> $species]);

        }elseif ($filterBy['slug'] == 'farmer-touch-report-fish'){
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-fish-excel.html.twig', ['entities'=>$entities, 'species'=> $species, 'filterBy'=> $filterBy]);
        }else{
            $html = $this->renderView('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-cattle-excel.html.twig', ['entities'=>$entities, 'species'=> $species, 'filterBy'=> $filterBy]);
        }

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();

    }
}