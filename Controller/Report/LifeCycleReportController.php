<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class FishLifeCycleReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
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
        $form = $this->createForm(SearchFilterFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $lifeCycleSlug = $form->getData()['lifeCycle']->getSlug();

            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['employee'] = $form->getData()['employee'];

            switch ($lifeCycleSlug){
                case 'boiler-life-cycle':
                    $entities = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->getChickLifeCycleDetails($lifeCycleSlug,$filterBy);
                    break;
                case 'sonali-life-cycle':
                    break;
                case 'layer-life-cycle-brown':
                    break;
                case 'layer-life-cycle-white':
                    break;
                case 'dairy-life-cycle':
                    break;
                case 'fattening-life-cycle':
                    break;
                case 'fish-life-cycle-report':
                    break;
                case 'fish-life-cycle-after-sale-report':
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
        ]);
    }
}