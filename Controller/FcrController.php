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
//use Terminalbd\CrmBundle\Entity\BroilerStandard;
//use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Form\FcrFormType;
use Terminalbd\CrmBundle\Repository\FcrRepository;


/**
 * @Route("/crm/fcr")
 */
class FcrController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="fcr")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Fcr::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/fcr/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/report", methods={"GET"}, name="fcr_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Fcr::class)->findAll();
        return $this->render('@TerminalbdCrm/fcr/report.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/after", methods={"GET"}, name="fcr_after")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index_after(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(Fcr::class)->findBy(
            ['fcrOfFeed'=>'AFTER']
        );
        return $this->render('@TerminalbdCrm/fcr/after_index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="fcr_new")
     */
    public function new(Request $request): Response
    {
        $data = $request->request->all();
        $entity = new Fcr();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(FcrFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo)) ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingReport = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReportByReportingDateAndFeedType($data['fcr_form'], $this->getUser());
            if($existingReport){
                $this->addFlash('danger', 'This month report already exist');
                return $this->redirectToRoute('fcr');
            }
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('fcr_new');
            }
            return $this->redirectToRoute('fcr');
        }
        return $this->render('@TerminalbdCrm/fcr/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="fcr_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, Fcr $entity): Response
    {
        $data = $request->request->get('fcr_form');

        if(date('Y-m-d', strtotime($data['reporting_month']))!=$entity->getReportingMonth()->format('Y-m-d')){
            $existingReport = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReportByReportingDateAndFeedType($data, $this->getUser());
            if($existingReport){
                $this->addFlash('danger', 'This month report already exist');
                return $this->redirectToRoute('fcr_edit',['id'=>$entity->getId()]);
            }
        }
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(FcrFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo)) ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('fcr');
            }
            return $this->redirectToRoute('fcr');
        }
        return $this->render('@TerminalbdCrm/fcr/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/{id}/delete", methods={"GET"}, name="fcr_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(Fcr::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    /**
     * @param Fcr $fcr
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/modal", methods={"GET", "POST"}, name="fcr_details_modal")
     */
    public function newModal(Request $request, Fcr $fcr): Response
    {
        $data = $request->request->all();
        $agents=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($fcr->getEmployee());


        return $this->render('@TerminalbdCrm/fcr/details-modal.html.twig', [
            'entity' => $fcr,
            'agents' => $agents,
        ]);
    }

    /**
     * @param Fcr $fcr
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/refresh", methods={"GET", "POST"}, name="fcr_details_refresh", options={"expose"=true})
     */
    public function fcrDetailsRefresh(Request $request, Fcr $fcr): Response
    {

        return $this->render('@TerminalbdCrm/fcr/partial/fcr-details.html.twig', [
            'entity' => $fcr,
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/details/add", methods={"POST"}, name="crm_fcr_detail_report_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function addFcrDetailsReport(Request $request, Fcr $fcr): Response
    {
        $data = $request->request->all();
        $agent = $this->getDoctrine()->getRepository(Agent::class)->find($data['agent']);

        $entity = new FcrDetails();
        $entity->setTotalBirds(isset($data['totalBirds'])&&$data['totalBirds']!=""?(float)$data['totalBirds']:0);
        $entity->setAgeDay(isset($data['ageDays'])&&$data['ageDays']!=""?(float)$data['ageDays']:0);
        $entity->setMortalityPes(isset($data['mortalityPes'])&&$data['mortalityPes']!=""?(float)$data['mortalityPes']:0);
        $entity->setMortalityPercent($entity->calculateMortalityPercent());
        $entity->setWeight(isset($data['weightAchieved'])?$data['weightAchieved']:0);
        $entity->setFeedConsumptionTotalKg(isset($data['feedTotalKg'])&&$data['feedTotalKg']!=""?(float)$data['feedTotalKg']:0);
        $entity->setFeedConsumptionPerBird($entity->calculatePerBird());
        $entity->setFcrWithoutMortality($entity->calculateWithoutMortality());
        $entity->setFcrWithMortality($entity->calculateWithMortality());
        $entity->setFeedType(isset($data['feedType'])?$data['feedType']:'');

        $hatchingDate = isset($data['hatchingDate'])&&$data['hatchingDate']!=""?date('Y-m-d',strtotime($data['hatchingDate'])):date('Y-m-d',strtotime('now'));
        $entity->setHatchingDate(new \DateTime($hatchingDate));
        $proDate = isset($data['proDate'])&&$data['proDate']!=""?date('Y-m-d',strtotime($data['proDate'])):date('Y-m-d',strtotime('now'));
        $entity->setProDate(new \DateTime($proDate));
        $entity->setHatchery(isset($data['hatchery'])?$data['hatchery']:'');
        $entity->setBreed(isset($data['breed'])?$data['breed']:'');
        $entity->setFeed(isset($data['feed'])?$data['feed']:'');
        $entity->setFeedMill(isset($data['feedMill'])?$data['feedMill']:'');
        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:'');
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:'');
        $entity->setAgent($agent?$agent:null);
        $entity->setFcr($fcr);

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'mortalityPercent'=>$entity->getMortalityPercent(),
                'data'=>$data,
                'status'=>200,
            )
        );

    }



}
