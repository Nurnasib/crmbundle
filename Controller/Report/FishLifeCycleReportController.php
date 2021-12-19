<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FishLifeCycleReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class FishLifeCycleReportController extends AbstractController
{
    /**
     * @Route("/crm/fish/life-cycle", methods={"GET","POST"}, name="crm_fish_life_cycle")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function index(Request $request)
    {
        $keepElement = [
            'employee' => '',
            'month' => '',
            'year' => ''];

        $searchForm = $this->createForm(SearchFilterFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = array_intersect_key($searchForm->getData(), $keepElement);
            return $this->redirectToRoute('crm_fish_life_cycle');
        }

        return $this->render('@TerminalbdCrm/report/fish/index.html.twig',[
            'form' => $searchForm->createView(),
        ]);
    }
}