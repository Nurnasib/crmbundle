<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

class FarmerTouchReportController extends AbstractController
{
    /**
     * @Route("/crm/farmer/{slug}", name="farmer-touch-report")
     * @param string $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function touchReport(string $slug, Request $request)
    {
        $entities = [];
        $filterBy = [];
        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($slug == 'farmer-touch-report-poultry'){
            if ($searchForm->isSubmitted()){
                $filterBy = $searchForm->getData();
                $filterBy['slug'] = $slug;
//                dd($filterBy);
                $entities = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFarmertouchReport($filterBy);
            }
            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-poultry.html.twig',['searchForm' =>$searchForm->createView(), 'entities' =>$entities]);

        }elseif ($slug == 'farmer-touch-report-fish'){
            if ($searchForm->isSubmitted()){
                $filterBy = $searchForm->getData();
                $filterBy['slug'] = $slug;
//                dd($filterBy);
                $entities = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFarmertouchReport($filterBy);
            }
            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-fish.html.twig',['searchForm' =>$searchForm->createView(), 'entities' =>$entities]);

        }else{

            return $this->render('@TerminalbdCrm/report/farmerTouch/report-farmer-touch-cattle.html.twig',['searchForm' =>$searchForm->createView()]);

        }
    }
}