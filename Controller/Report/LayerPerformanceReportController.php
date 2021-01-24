<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Terminalbd\CrmBundle\Entity\LayerPerformance;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class LayerPerformanceReportController extends AbstractController
{
    /**
     * @Route("/crm/layer/performance/report", methods={"GET","POST"}, name="layer_performance_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
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
            $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->getLayerPerformanceReport($filterBy);
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

        $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->getLayerPerformanceReport($filteBy);

        $fileName = 'layer_performance_report'.'_'.time().'.xls';
        $html = $this->renderView('@TerminalbdCrm/report/layer/report-performance-excel.html.twig',['entities'=>$entities]);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachement; filename=$fileName");

        echo $html;
        die();
    }

}