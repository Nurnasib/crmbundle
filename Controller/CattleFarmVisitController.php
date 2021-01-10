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
use Terminalbd\CrmBundle\Entity\CattleFarmVisit;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CattlePerformance;
use Terminalbd\CrmBundle\Entity\CattlePerformanceDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\CattleFarmVisitDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/cattle/farm/visit")
 */
class CattleFarmVisitController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="cattle_farm_visit_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $entity = new CattleFarmVisit();
        $existReport = $this->getDoctrine()->getRepository(CattleFarmVisit::class)->getCattleFarmVisitReportByReportingDateCustomerAndEmployee($report, $this->getUser());
        if ($existReport){

            return $this->redirectToRoute('cattle_farm_visit_details_modal', ['id'=>$existReport->getId(), 'customerId'=>$crmCustomer->getId()]);
        }

            $reportingDate = date('Y-m-d',strtotime('now'));

            $entity->setReportingMonth(new \DateTime($reportingDate));
            $entity->setReport($report);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');

        return $this->redirectToRoute('cattle_farm_visit_details_modal', ['id'=>$entity->getId(), 'customerId'=>$crmCustomer->getId()]);

    }

    /**
     * @param CattleFarmVisit $cattleFarmVisit
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="cattle_farm_visit_details_modal", options={"expose"=true})
     */
    public function cattleFarmVisitDetailsModal( Request $request, CattleFarmVisit $cattleFarmVisit ): Response
    {
        $customer = null;
        $entity = new CattleFarmVisitDetails();

        $form = $this->createForm(CattleFarmVisitDetailsFormType::class, $entity,array('user' => $this->getUser(),'report' => $cattleFarmVisit->getReport()));

//

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getcustomer = $request->query->get('customer');
            if($getcustomer){

                $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($getcustomer);
            }
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            $entity->setCrmCattleFarmVisit($cattleFarmVisit);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }
        return $this->render('@TerminalbdCrm/cattleFarmVisit/cattle-farm-visit-details-modal.html.twig', [
            'cattleFarmVisit' => $cattleFarmVisit,
            'form' => $form->createView(),
            'customer'=>$request->query->get('customerId')
        ]);
    }

    /**
     * @param CattleFarmVisit $cattleFarmVisit
     * @Route("/detail/{id}/refresh", methods={"GET"}, name="crm_cattle_farm_visit_detail_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function cattleFarmVisitDetailsReportRefresh(CattleFarmVisit $cattleFarmVisit): Response
    {
        return $this->render('@TerminalbdCrm/cattleFarmVisit/partial/cattle-farm-visit-details-body.html.twig', [
            'cattleFarmVisit' => $cattleFarmVisit,
        ]);
    }
    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="cattle_farm_visit_detail_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    /**
     * @param $report
     * @Route("/life/cycle/{report}", methods={"GET"}, name="crm_cattle_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report): Response
    {

        $entities = $this->getDoctrine()->getRepository(CattleLifeCycle::class)->getCattleLifeCycleByReportType($report);
        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report.html.twig',['entities' => $entities]);
    }

    /**
     * @param CattleLifeCycle $cattleLifeCycle
     * @Route("/life/cycle/{id}/report", methods={"GET"}, name="crm_cattle_report_detail")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportDetails( CattleLifeCycle $cattleLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report-details.html.twig',['cattleLifeCycle' => $cattleLifeCycle]);
    }


}
