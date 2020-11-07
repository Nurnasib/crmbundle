<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Core\Agent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\CattleLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/cattle")
 */
class CattleLifeCycleController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="cattle_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {

        $entity = new CattleLifeCycle();
        $existReport = $this->getDoctrine()->getRepository(CattleLifeCycle::class)->findOneBy(array('customer'=>$crmCustomer, 'report'=>$report, 'lifeCycleState'=>CattleLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS));
        if ($existReport){
            $entity= $existReport;
        }

        if($existReport){
            return $this->redirectToRoute('cattle_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }
        $data = $request->request->all();

        $form = $this->createForm(CattleLifeCycleFormType::class, $entity,array('user' => $this->getUser()))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportingDate = isset($data['reporting_date'])?date('Y-m-d',strtotime($data['reporting_date'])):date('Y-m-d',strtotime('now'));

            $entity->setReportingDate(new \DateTime($reportingDate));
            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setLifeCycleState(CattleLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return new Response('success');
              //  return $this->redirectToRoute('cattle_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
            }
            return $this->redirectToRoute('cattle_new_modal');
        }
        return $this->render('@TerminalbdCrm/cattleLifecycle/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing CattleLifeCycle entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_cattle_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, CattleLifeCycle $entity): Response
    {
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(CattleLifeCycleFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_cattle', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('cattle_new');
        }
        return $this->render('@TerminalbdCrm/cattleLifecycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="cattle_life_cycle_details_modal", options={"expose"=true})
     */
    public function lifeCycleDetailsModal( Request $request, CattleLifeCycle $cattleLifeCycle): Response
    {
        $data = $request->request->all();
        $entity = new CattleLifeCycleDetails();

        if($cattleLifeCycle->getReport()->getSlug()=='dairy-life-cycle'){
            $form = $this->createForm(DairyLifeCycleDetailsFormType::class, $entity,array('user' => $this->getUser()))
                ->add('SaveAndCreate', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-sm')));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $visitingDate = isset($data['dairy_life_cycle_details_form']['visiting_date'])?date('Y-m-d',strtotime($data['dairy_life_cycle_details_form']['visiting_date'])):date('Y-m-d',strtotime('now'));

                $entity->setVisitingDate(new \DateTime($visitingDate));

                $entity->setAverageWeightPerKgConsumptionFeed($entity->calculateAverageWeightPerKgConsumptionFeed());
                $entity->setAverageWeightPerKgDm($entity->calculateAverageWeightPerKgDm());
                $entity->setConsumptionFeedIntakeTotal($entity->calculationConsumptionFeedIntakeTotal());
                $entity->setDmOfFodderGreenGrassKg($entity->calculateDmOfFodderGreenGrassKg());
                $entity->setDmOfFodderStrawKg($entity->calculateDmOfFodderStrawKg());
                $entity->setTotalDmKg($entity->calculateTotalDmKg());
                $entity->setDmRequirementByBwtKg($entity->calculateDmRequirementByBwtKgForDairy());


                $entity->setCrmCattleLifeCycle($cattleLifeCycle);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->addFlash('success', 'post.created_successfully');
                if ($form->get('SaveAndCreate')->isClicked()) {
                    return new Response('success');
//                      return $this->redirectToRoute('cattle_life_cycle_details_modal', ['id'=>$cattleLifeCycle->getId()]);
                }
            }
            return $this->render('@TerminalbdCrm/cattleLifecycle/dairy-life-cycle-details-report-modal.html.twig', [
                'cattleLifeCycle' => $cattleLifeCycle,
                'form' => $form->createView(),
            ]);
        }

        $form = $this->createForm(CattleLifeCycleDetailsFormType::class, $entity,array('user' => $this->getUser()))
            ->add('SaveAndCreate', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-sm')));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $visitingDate = isset($data['cattle_life_cycle_details_form']['visiting_date'])?date('Y-m-d',strtotime($data['cattle_life_cycle_details_form']['visiting_date'])):date('Y-m-d',strtotime('now'));

            $entity->setVisitingDate(new \DateTime($visitingDate));

            $entity->setBodyWeightDifference($entity->calculateBodyWeightDifference());
            $entity->setAverageWeightPerDay($entity->calculateAverageWeightPerDay());
            $entity->setAverageWeightPerKgConsumptionFeed($entity->calculateAverageWeightPerKgConsumptionFeed());
            $entity->setAverageWeightPerKgDm($entity->calculateAverageWeightPerKgDm());
            $entity->setConsumptionFeedIntakeTotal($entity->calculationConsumptionFeedIntakeTotal());
            $entity->setDmOfFodderGreenGrassKg($entity->calculateDmOfFodderGreenGrassKg());
            $entity->setDmOfFodderStrawKg($entity->calculateDmOfFodderStrawKg());
            $entity->setTotalDmKg($entity->calculateTotalDmKg());
            $entity->setDmRequirementByBwtKg($entity->calculateDmRequirementByBwtKg());


            $entity->setCrmCattleLifeCycle($cattleLifeCycle);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return new Response('success');
                //  return $this->redirectToRoute('cattle_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
            }
        }
        return $this->render('@TerminalbdCrm/cattleLifecycle/fattening-life-cycle-details-report-modal.html.twig', [
            'cattleLifeCycle' => $cattleLifeCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CattleLifeCycle $cattleLifeCycle
     * @Route("/life-cycle/{id}/refresh", methods={"GET"}, name="crm_cattle_life_cycle_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function cattleLifeCycleReportRefresh(CattleLifeCycle $cattleLifeCycle): Response
    {
        if($cattleLifeCycle->getReport()->getSlug()=='dairy-life-cycle') {
            return $this->render('@TerminalbdCrm/cattleLifecycle/partial/dairy-life-cycle.html.twig', [
                'cattleLifeCycle' => $cattleLifeCycle,
            ]);
        }
        return $this->render('@TerminalbdCrm/cattleLifecycle/partial/fattening-life-cycle.html.twig', [
            'cattleLifeCycle' => $cattleLifeCycle,
        ]);
    }

    /**
     * @param CattleLifeCycle $cattleLifeCycle
     * @Route("/life-cycle/{id}/complete", methods={"POST"}, name="crm_cattle_life_cycle_complete", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function cattleLifeCycleReportComplete(CattleLifeCycle $cattleLifeCycle): Response
    {
        $cattleLifeCycle->setLifeCycleState(CattleLifeCycle::LIFE_CYCLE_STATE_COMPLETE);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cattleLifeCycle);
        $em->flush();
        return new JsonResponse(array(
            'message'=>"Success",
            'status'=>200
        ));
    }
    



}
