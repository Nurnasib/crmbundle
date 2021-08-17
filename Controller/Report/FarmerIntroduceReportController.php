<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class FarmerIntroduceReportController extends AbstractController
{
    /**
     * @Route("/crm/farmer/farmer-introduce-report",methods={"GET","POST"}, name="farmer_introduce_report")
     * @param Request $request
     * @param $breedType
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function farmerIntroduceReport(Request $request)
    {
        $entities = [];
        $filterBy = [];
        $species = [];
        $breedTypeSlug = $request->query->get('breedType');
        $breedType = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('settingType'=>'BREED_NAME','slug'=>$breedTypeSlug));

        $species = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesForFarmerTouchReport($breedType->getId());

        $searchForm = $this->createForm(SearchFilterFormType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['employeeId'] = $filterBy['employee']->getId();
            $filterBy['breedType'] = $breedType->getSlug();
            $filterBy['bOfYear'] = date('Y') . '-01-01 00:00:00';
            $filterBy['eOfYear'] = date('Y') . '-12-31 23:59:59';

            $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReport($filterBy);
        }

        if ($breedType->getSlug() == 'poultry-breed'){
            return $this->render('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-poultry.html.twig', ['searchForm'=>$searchForm->createView(), 'entities'=>$entities, 'filterBy' =>$filterBy, 'species'=> $species]);

        }elseif ($breedType->getSlug() == 'fish-breed'){
            return $this->render('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-fish.html.twig', ['searchForm'=>$searchForm->createView(), 'entities'=>$entities, 'filterBy' =>$filterBy, 'species'=> $species]);

        }else{
            return $this->render('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-cattle.html.twig', ['searchForm'=>$searchForm->createView(), 'entities'=>$entities, 'filterBy' =>$filterBy, 'species'=> $species]);

        }
    }


    /**
     * @Route("/crm/farmer/farmer-introduce-report-excel", name="farmer_introduce_report_excel")
     * @param Request $request
     */
    public function farmerIntroduceReportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
        $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReport($filterBy);

        $fileName = $filterBy['breedType'] . '_' . time() . '.xls';


        if ($filterBy['breedType'] == 'poultry-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-poultry-excel.html.twig', ['entities'=>$entities, 'species'=> $species, 'filterBy'=>$filterBy]);

        }elseif ($filterBy['breedType'] == 'fish-breed'){
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-fish-excel.html.twig', ['entities'=>$entities, 'species'=> $species, 'filterBy'=> $filterBy]);
        }else{
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-cattle-excel.html.twig', ['entities'=>$entities, 'species'=> $species, 'filterBy'=> $filterBy]);
        }

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();

    }


    /**
     * @Route("/crm/farmer/farmer-introduce-report-pdf", name="farmer_introduce_report_pdf")
     * @param Request $request
     */
    public function farmerIntroduceReportPdf(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);
        $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReport($filterBy);


        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        if ($filterBy['breedType'] == 'poultry-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-poultry-pdf.html.twig',['entities' => $entities, 'species'=> $species, 'filterBy' => $filterBy]);

        }elseif ($filterBy['breedType'] == 'fish-breed'){
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-fish-pdf.html.twig',['entities' => $entities, 'species'=>$species, 'filterBy'=> $filterBy]);
//            $html = 'Hello';
        }else{
            $html = $this->renderView('@TerminalbdCrm/report/farmerIntroduce/report-introduce-new-farmer-list-nourish-cattle-pdf.html.twig',['entities' => $entities, 'species'=>$species, 'filterBy'=> $filterBy]);
        }

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($filterBy['breedType'] . "_" . time() . ".pdf", [
            "Attachment" => false
        ]);

    }

}