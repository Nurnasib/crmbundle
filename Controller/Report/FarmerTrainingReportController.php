<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FarmerTrainingReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class FarmerTrainingReportController extends AbstractController
{
    /**
     * @Route("/crm/farmer/training-report", methods={"GET","POST"}, name="farmer_training_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trainingReport(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $species = [];
        $trainingMaterial = [];
        $breedTypeSlug = $request->query->get('breedType');

        $breedType = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('settingType'=>'BREED_NAME','slug'=>$breedTypeSlug));
//        dd($breedType);
        $materials = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedType->getId()));
//        dd($materials);

        foreach ($materials as $material){
            $trainingMaterial[] = array('id'=>$material->getId(), 'name'=>$material->getName());
        }
//        dd($trainingMaterial);

        $species = $this->getDoctrine()->getRepository(Setting::class)->getSpeciesForFarmerTouchReport($breedType->getId());

        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted())
        {
            $filterBy = $searchForm->getData();
            $filterBy['breedTypeSlug'] = $breedTypeSlug;
            $filterBy['employeeId'] = $filterBy['employee']->getId();

            $filterBy['bOfYear'] = date('Y') . '-01-01';
            $filterBy['eOfYear'] = date('Y') . '-12-31';

//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->getFarmerTrainingReport($filterBy);
//            dd($entities);
        }

        if ($breedTypeSlug == 'poultry-breed'){
            return $this->render('@TerminalbdCrm/report/farmerTraining/report-farmer-training-poultry.html.twig',[
                'searchForm' => $searchForm->createView(),
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $trainingMaterial
            ]);
        }elseif ($breedTypeSlug == 'fish-breed'){
            return $this->render('@TerminalbdCrm/report/farmerTraining/report-farmer-training-fish.html.twig',[
                'searchForm' => $searchForm->createView(),
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $trainingMaterial
            ]);
        }else{
            return $this->render('@TerminalbdCrm/report/farmerTraining/report-farmer-training-cattle.html.twig',[
                'searchForm' => $searchForm->createView(),
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $trainingMaterial
            ]);
        }
    }

    /**
     * @Route("/crm/farmer/training-report-excel", name="farmer_training_report_excel")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trainingReportExcel(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
        $materials = $request->query->get('materials');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->getFarmerTrainingReport($filterBy);


        $fileName = 'farmer_training_report_' . $filterBy['breedTypeSlug'] . '_' . time() . '.xls';

        if ($filterBy['breedTypeSlug'] == 'poultry-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-poultry-excel.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }elseif ($filterBy['breedTypeSlug'] == 'fish-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-fish-excel.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }
        else{
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-cattle-excel.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die();
    }

    /**
     * @Route("/crm/farmer/training-report-pdf", name="farmer_training_report_pdf")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trainingReportPdf(Request $request)
    {
        $filterBy = $request->query->get('filterBy');
        $species = $request->query->get('species');
        $materials = $request->query->get('materials');
        $filterBy['employee'] = $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']);

        $entities = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->getFarmerTrainingReport($filterBy);


        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        if ($filterBy['breedTypeSlug'] == 'poultry-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-poultry-pdf.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }elseif ($filterBy['breedTypeSlug'] == 'fish-breed'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-fish-pdf.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }else{
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/farmerTraining/report-farmer-training-cattle-pdf.html.twig',[
                'filterBy' => $filterBy,
                'entities' => $entities,
                'species' => $species,
                'materials' => $materials
            ]);
        }

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A2', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("farmer_training_report_" . $filterBy['breedTypeSlug'] . "_" . time() . ".pdf", [
            "Attachment" => false
        ]);

    }

}