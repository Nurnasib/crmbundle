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
 * @Security("is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_DEVELOPER)")
 */
class CattleFarmVisitController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="cattle_farm_visit_new_modal", options={"expose"=true})
     * @return Response
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {

        return $this->redirectToRoute('cattle_farm_visit_details_modal', ['id'=>$report->getId(), 'customerId'=>$crmCustomer->getId()]);

    }

    /**
     * @param Request $request
     * @param Setting $report
     * @return Response
     * @throws \Exception
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="cattle_farm_visit_details_modal", options={"expose"=true})
     */
    public function cattleFarmVisitDetailsModal( Request $request, Setting $report ): Response
    {
        $customer = null;
        $entity = new CattleFarmVisitDetails();

        $form = $this->createForm(CattleFarmVisitDetailsFormType::class, $entity,array('user' => $this->getUser(),'report' => $report));

//

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getCustomer = $request->query->get('customer');
            if($getCustomer){

                $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($getCustomer);
            }

            $reportingDate = date('Y-m-d',strtotime('now'));

            $entity->setReportingMonth(new \DateTime($reportingDate));

            $entity->setReport($report);
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            $entity->setEmployee($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }
        $cattleFarmVisitDetails = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->getCattleFarmVisitReportByReportingDateCustomerAndEmployee($report, $this->getUser());
        return $this->render('@TerminalbdCrm/cattleFarmVisit/cattle-farm-visit-details-modal.html.twig', [
            'report' => $report,
            'cattleFarmVisitDetails' => $cattleFarmVisitDetails,
            'form' => $form->createView(),
            'customer'=>$request->query->get('customerId')
        ]);
    }

    /**
     * @param Setting $report
     * @Route("/detail/{id}/refresh", methods={"GET"}, name="crm_cattle_farm_visit_detail_refresh", options={"expose"=true})
     * @return Response
     */
    public function cattleFarmVisitDetailsReportRefresh( Setting $report): Response
    {
        $cattleFarmVisitDetails = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->getCattleFarmVisitReportByReportingDateCustomerAndEmployee($report, $this->getUser());
        return $this->render('@TerminalbdCrm/cattleFarmVisit/partial/cattle-farm-visit-details-body.html.twig', [
            'cattleFarmVisitDetails' => $cattleFarmVisitDetails,
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="cattle_farm_visit_detail_delete")
     * @param $id
     * @return Response
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


}
