<?php


namespace Terminalbd\CrmBundle\Controller\Report;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;


class ChickLifeCycleReportController extends AbstractController
{
    /**
     * @param $report
     * @Route("/crm/chick/{report}", methods={"GET","POST"}, name="crm_chick_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report, Request $request): Response
    {
        $entities=[];
        $searchForm = $this->createForm(SearchFilterFormType::class)->remove('employee')->remove('startDateCreated')->remove('endDateCreated');
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
            $filterBy['slug'] = $report;
//            dd($filterBy);

            $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($filterBy);
//            dd($entities);

        }
        return $this->render('@TerminalbdCrm/report/chick/report-life-cycle.html.twig',['searchForm' => $searchForm->createView(), 'entities' => $entities]);
    }

}