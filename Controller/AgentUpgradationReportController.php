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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\AgentUpgradationReport;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\AgentUpgradationReportFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/agent/upgradation/report")
 */
class AgentUpgradationReportController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/agent/{id}/purpose/{purpose}/new/modal", methods={"GET", "POST"}, name="agent_upgradation_report_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, Agent $agent, Setting $purpose): Response
    {

        $entity = new AgentUpgradationReport();


        $form = $this->createForm(AgentUpgradationReportFormType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setAgent($agent);
            $entity->setAgentPurpose($purpose);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new JsonResponse(array('status'=>200, 'id'=>$entity->getId()));
        }

        $agentUpgradationReports = $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->getAgentUpgradationReportByCreatedDateEmployeeReport($purpose, $this->getUser());

        return $this->render('@TerminalbdCrm/agentUpgradationReport/new-modal.html.twig', [
            'purpose' => $purpose,
            'agent' => $agent,
            'entity' => $entity,
            'agentUpgradationReports' => $agentUpgradationReports,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param AgentUpgradationReport $agentUpgradationReport
     * @Route("/{id}/refresh", methods={"GET"}, name="agent_upgradation_report_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function reportRefresh(AgentUpgradationReport $agentUpgradationReport): Response
    {
        $agentUpgradationReports = $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->getAgentUpgradationReportByCreatedDateEmployeeReport($agentUpgradationReport->getAgentPurpose(), $this->getUser());

        return $this->render('@TerminalbdCrm/agentUpgradationReport/details-body.html.twig', [
            'agentUpgradationReports' => $agentUpgradationReports,
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/{id}/delete", methods={"POST"}, name="agent_upgradation_report_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


}
