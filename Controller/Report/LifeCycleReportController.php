<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\LayerLifeCycleDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FishLifeCycleReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/life-cycle", name="")
 */
class LifeCycleReportController extends AbstractController
{
    /**
     * @Route("/", name="life_cycle_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $filterBy = [];
        $entities = [];
        $lifeCycleSlug = '';
        $employee = null;
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $lifeCycleSlug = $form->getData()['lifeCycle']->getSlug();

            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['reportStatus'] = $form->getData()['reportStatus'];
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['farmerId'] = $request->request->get('search_filter_form')['employeeWiseFarmer'];

            $employee = $form->getData()['employee'];

            switch ($lifeCycleSlug){
                case 'boiler-life-cycle':
                case 'sonali-life-cycle':
                    $entities = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->getChickLifeCycleDetails($lifeCycleSlug,$filterBy);
                    break;
                case 'layer-life-cycle-brown':
                case 'layer-life-cycle-white':
                    $entities = $this->getDoctrine()->getRepository(LayerLifeCycleDetails::class)->getLayerLifeCycleDetails($lifeCycleSlug,$filterBy);

                    break;
                case 'dairy-life-cycle':
                case 'fattening-life-cycle':
                    $entities = $this->getDoctrine()->getRepository(CattleLifeCycleDetails::class)->getCattleLifeCycleDetails($lifeCycleSlug,$filterBy);

                break;
                case 'fish-life-cycle-report':
                case 'fish-life-cycle-after-sale-report':
                    $entities = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetails($lifeCycleSlug,$filterBy);
                break;
                default:
                    $entities = [];
                    break;
            }

        }
        return $this->render('@TerminalbdCrm/report/lifeCycle/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'filterBy'=> $filterBy,
            'lifeCycleSlug'=> $lifeCycleSlug,
            'employee'=> $employee,
        ]);
    }

    /**
     * @Route("/pdf", name="life_cycle_pdf")
     * @param Request $request
     */
    public function pdf(Request $request)
    {
       $filterBy = $request->query->get('filterBy');
       $employee = $filterBy['employeeId'] ? $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']) : '';

        switch ($filterBy['lifeCycle']){
            case 'boiler-life-cycle':
            case 'sonali-life-cycle':
                $entities = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->getChickLifeCycleDetails($filterBy['lifeCycle'],$filterBy);
                break;
            case 'layer-life-cycle-brown':
            case 'layer-life-cycle-white':
            $entities = $this->getDoctrine()->getRepository(LayerLifeCycleDetails::class)->getLayerLifeCycleDetails($filterBy['lifeCycle'],$filterBy);

            break;
            case 'dairy-life-cycle':
            case 'fattening-life-cycle':
                $entities = $this->getDoctrine()->getRepository(CattleLifeCycleDetails::class)->getCattleLifeCycleDetails($filterBy['lifeCycle'],$filterBy);

            break;
            case 'fish-life-cycle-report':
            case 'fish-life-cycle-after-sale-report':
            $entities = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetails($filterBy['lifeCycle'],$filterBy);
            break;
            default:
                $entities = [];
                break;
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial, sans-serif');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/report/lifeCycle/pdf.html.twig',[
            'lifeCycleSlug' => $filterBy['lifeCycle'],
            'employee' => $employee,
            'entities' => $entities
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($filterBy['lifeCycle'] . '_'.time() . ".pdf", [
            "Attachment" => false
        ]);
        die();
    }
    /**
     * @Route("/excel", name="life_cycle_excel")
     * @param Request $request
     */
    public function excel(Request $request)
    {

        $filterBy = $request->query->get('filterBy');
        $employee = $filterBy['employeeId'] ? $this->getDoctrine()->getRepository(User::class)->find($filterBy['employeeId']) : '';

        switch ($filterBy['lifeCycle']){
            case 'boiler-life-cycle':
            case 'sonali-life-cycle':
                $entities = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->getChickLifeCycleDetails($filterBy['lifeCycle'],$filterBy);
                break;
            case 'layer-life-cycle-brown':
            case 'layer-life-cycle-white':
            $entities = $this->getDoctrine()->getRepository(LayerLifeCycleDetails::class)->getLayerLifeCycleDetails($filterBy['lifeCycle'],$filterBy);
            break;
            case 'dairy-life-cycle':
            case 'fattening-life-cycle':
                $entities = $this->getDoctrine()->getRepository(CattleLifeCycleDetails::class)->getCattleLifeCycleDetails($filterBy['lifeCycle'],$filterBy);

            break;
            case 'fish-life-cycle-report':
            case 'fish-life-cycle-after-sale-report':
                $entities = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetails($filterBy['lifeCycle'],$filterBy);
            break;
            default:
                $entities = [];
                break;
        }

        $html = $this->renderView('@TerminalbdCrm/report/lifeCycle/excel.html.twig',[
            'lifeCycleSlug' => $filterBy['lifeCycle'],
            'employee' => $employee,
            'entities' => $entities
        ]);

        $fileName = $filterBy['lifeCycle'].'_'.time().".xls";

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $html;
        die;
    }


    /**
     * @Route("/ajax/farmer-by-employee-location/{id}", name="get_farmer_by_employee_location", options={"expose" = true})
     * @param $id
     * @return JsonResponse
     */
    public function getFarmerByEmployee($id)
    {
        $employee = $this->getDoctrine()->getRepository(User::class)->find($id);
        $farmers =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($employee,'farmer');

        $data = [];

        foreach ($farmers as $farmer) {
            $data[$farmer['id']] = $farmer['name'];
        }

        return new JsonResponse($data);

    }
}