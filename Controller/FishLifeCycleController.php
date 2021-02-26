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
use Terminalbd\CrmBundle\Entity\FishLifeCycle;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\FishLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/fish/life/cycle")
 */
class FishLifeCycleController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="fish_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {


        $existReport = $this->getDoctrine()->getRepository(FishLifeCycle::class)->getFishReportByReportingDateAndFeedType($report, $crmCustomer, $this->getUser());

        if($existReport){
            return $this->redirectToRoute('fish_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }

        $entity = new FishLifeCycle();

        $reportingDate = date('Y-m-d',strtotime('now'));

        $entity->setReportingMonth(new \DateTime($reportingDate));
        $entity->setCustomer($crmCustomer);
        $entity->setReport($report);
        $entity->setEmployee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.created_successfully');

        return $this->redirectToRoute('fish_life_cycle_details_modal', ['id'=>$entity->getId()]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="fish_life_cycle_details_modal", options={"expose"=true})
     */
    public function lifeCycleDetailsModal( Request $request, FishLifeCycle $fishLifeCycle): Response
    {
        $data = $request->request->all();
        $entity = new FishLifeCycleDetails();

        $form = $this->createForm(FishLifeCycleDetailsFormType::class, $entity,array('user' => $this->getUser(),'report' => $fishLifeCycle->getReport()));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $reporting_date = isset($data['fish_life_cycle_details_form']['reporting_date'])?date('Y-m-d',strtotime($data['fish_life_cycle_details_form']['reporting_date'])):date('Y-m-d',strtotime('now'));

            $entity->setReportingDate(new \DateTime($reporting_date));

            $reporting_date = isset($data['fish_life_cycle_details_form']['reporting_date'])?date('Y-m-d',strtotime($data['fish_life_cycle_details_form']['reporting_date'])):date('Y-m-d',strtotime('now'));

            $entity->setReportingDate(new \DateTime($reporting_date));


            $entity->setCustomer($fishLifeCycle->getCustomer());
            $entity->setAgent($fishLifeCycle->getCustomer()->getAgent());
            $entity->setFishLifeCycle($fishLifeCycle);

            $entity->setStockingDensity($entity->getCalculateStockingDensity());
            $entity->setCurrentCultureDays($entity->calculateCurrentCultureDays());

            $entity->setWeightGainGm($entity->calculateWeightGainGm());
            $entity->setWeightGainKg($entity->calculateWeightGainKg());

            $entity->setCurrentFcr($entity->calculateCurrentFcr());
            $entity->setCurrentAdg($entity->calculateCurrentAdg());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }
        return $this->render('@TerminalbdCrm/fishLifeCycle/fish-life-cycle-details-report-modal.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/{id}/refresh", methods={"GET"}, name="crm_fish_life_cycle_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function fishLifeCycleReportRefresh(FishLifeCycle $fishLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/fishLifeCycle/partial/fish-life-cycle-details-body.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="fish_life_cycle_detail_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @param $report
     * @Route("/life/cycle/{report}", methods={"GET"}, name="crm_fish_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report): Response
    {

        $entities = $this->getDoctrine()->getRepository(FishLifeCycle::class)->getFishLifeCycleByReportType($report);
        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report.html.twig',['entities' => $entities]);
    }

    /**
     * @param FishLifeCycle $cattleLifeCycle
     * @Route("/life/cycle/{id}/report", methods={"GET"}, name="crm_fish_report_detail")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportDetails( FishLifeCycle $cattleLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report-details.html.twig',['cattleLifeCycle' => $cattleLifeCycle]);
    }


}
