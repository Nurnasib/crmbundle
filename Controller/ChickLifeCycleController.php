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
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\ChickLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/chick")
 */
class ChickLifeCycleController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_chick")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        //  broiler index page
        $entitys = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/chickLifecycle/index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Route("/sonali", methods={"GET"}, name="crm_sonali")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function sonali_index(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findBy(
            ['birdMode'=>'SONALI']
        );
        return $this->render('@TerminalbdCrm/chickLifecycle/sonali_index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="chick_new")
     */
    public function new(Request $request): Response
    {
        $entity = new ChickLifeCycle();
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('chick_new');
            }
            return $this->redirectToRoute('chick_new');
        }
        return $this->render('@TerminalbdCrm/chickLifecycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @param CrmCustomer $crmCustomer
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="chick_new_modal")
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {


        $entity = new ChickLifeCycle();
        $existReport = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findOneBy(array('customer'=>$crmCustomer, 'report'=>$report, 'lifeCycleState'=>ChickLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS));
        if ($existReport){
            $entity= $existReport;
        }

        if($existReport){
            return $this->redirectToRoute('chick_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }
        $data = $request->request->all();
        $getRequestData = $_REQUEST;
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser()))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentTime = date('H:i:s',strtotime('now'));

            $reportingDate = isset($data['reporting_date'])?date('Y-m-d',strtotime($data['reporting_date'])):date('Y-m-d',strtotime('now'));
//            $reportingDate = $reporting_date.' '.$currentTime;

            $requestDate = isset($data['hatching_date'])?date('Y-m-d',strtotime($data['hatching_date'])):date('Y-m-d',strtotime('now'));
            $hatingDate = $requestDate.' '.$currentTime;

            $entity->setReportingDate(new \DateTime($reportingDate));
            $entity->setHatchingDate(new \DateTime($hatingDate));
            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setLifeCycleState(ChickLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('chick_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
            }
            return $this->redirectToRoute('chick_new_modal');
        }
        return $this->render('@TerminalbdCrm/chickLifecycle/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="chick_life_cycle_details_modal")
     */
    public function lifeCycleDetailsModal(ChickLifeCycle $chickLifeCycle): Response
    {
        $lifeCycleSetting = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findOneBy(array('report'=>$chickLifeCycle->getReport()));
        $crmChickLifeCycleDetails = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->findOneBy(array('crmChickLifeCycle'=>$chickLifeCycle->getId()));
        if (!$crmChickLifeCycleDetails){
            for($i=1; $i<=$lifeCycleSetting->getNumberOfWeek(); $i++){
               $chickLifeCycleDetails = new ChickLifeCycleDetails();

               $chickLifeCycleDetails->setVisitingWeek($i);
               $chickLifeCycleDetails->setCrmChickLifeCycle($chickLifeCycle);
               $chickLifeCycleDetails->setCreatedAt(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($chickLifeCycleDetails);

                $em->flush();
            }
        }

        return $this->render('@TerminalbdCrm/chickLifecycle/'.$chickLifeCycle->getReport()->getSlug().'-modal.html.twig', [
            'chickLifeCycle' => $chickLifeCycle,
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_chick_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, ChickLifeCycle $entity): Response
    {
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_chick', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('chick_new');
        }
        return $this->render('@TerminalbdCrm/chickLifecycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/life-cycle/{id}/edit", methods={"POST"}, name="crm_chick_life_cycle_edit", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function editLifeCycleDetails(Request $request, ChickLifeCycleDetails $entity): Response
    {
        $data = $request->request->all();

        $entity->setTotalBirds(isset($data['totalBirds'])?$data['totalBirds']:0);
        $entity->setAgeDays(isset($data['ageDays'])?$data['ageDays']:0);
        $entity->setMortalityPes(isset($data['mortalityPes'])?$data['mortalityPes']:0);
        $entity->setMortalityPercent($entity->calculateMortalityPercent());
        $entity->setWeightStandard(isset($data['weightStandard'])?$data['weightStandard']:0);
        $entity->setWeightAchieved(isset($data['weightAchieved'])?$data['weightAchieved']:0);
        $entity->setFeedTotalKg(isset($data['feedTotalKg'])?$data['feedTotalKg']:0);
        $entity->setPerBird($entity->calculatePerBird());
        $entity->setFeedStandard(isset($data['feedStandard'])?$data['feedStandard']:0);
        $entity->setWithoutMortality(isset($data['withoutMortality'])?$data['withoutMortality']:0);
        $entity->setWithMortality(isset($data['withMortality'])?$data['withMortality']:0);
        $entity->setFeedType(isset($data['feedType'])?$data['feedType']:null);

        $currentTime = date('H:i:s',strtotime('now'));
        $proDate = isset($data['proDate'])&&$data['proDate']!=""?date('Y-m-d',strtotime($data['proDate'])):date('Y-m-d',strtotime('now'));
        $proDate = $proDate.' '.$currentTime;
        $entity->setProDate(new \DateTime($proDate));
        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:null);
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'mortalityPercent'=>$entity->getMortalityPercent(),
                'perBird'=>$entity->getPerBird(),
                'data'=>$data,
                'status'=>200,
            )
        );

    }

    /**
     * Deletes a ChickLifeCycle entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_chick_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    



}
