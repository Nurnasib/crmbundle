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
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Form\FcrDetailsForAfterFormType;
use Terminalbd\CrmBundle\Form\FcrDetailsFormType;
use Terminalbd\CrmBundle\Form\FcrFormType;
use Terminalbd\CrmBundle\Repository\FcrRepository;


/**
 * @Route("/crm/fcr")
 */
class FcrController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{customer}/{report}/{afterBefore}/new", methods={"GET", "POST"}, name="fcr_new", options={"expose"=true})
     */
    public function new(Request $request, CrmCustomer $customer, Setting $report, $afterBefore): Response
    {
        $fcrAllReports = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReportByReportingDateAndFeedType($afterBefore, $report, $this->getUser());

        $entity = new FcrDetails();

        $form = $this->createForm(FcrDetailsFormType::class, $entity,array('user' => $this->getUser(), 'report' => $report));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reportingDate = date('Y-m-d',strtotime('now'));
            $entity->setReportingMonth(new \DateTime($reportingDate));
            $entity->setFcrOfFeed(strtoupper($afterBefore));
            $entity->setCustomer($customer);
            $entity->setReport($report);
            $entity->setAgent($customer->getAgent());
            $entity->setEmployee($this->getUser());


            if(in_array($report->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

                /* @var SonaliStandard $sonaliStandard*/
                $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
                if($sonaliStandard){
                    $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                    $entity->setFeedConsumptionStandard($sonaliStandard->getCumulativeFeedIntake());
                }
            }
            if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

                /* @var BroilerStandard $broilerStandard*/
                $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
                if($broilerStandard){
                    $entity->setWeightStandard($broilerStandard->getTargetBodyWeight());
                    $entity->setFeedConsumptionStandard($broilerStandard->getTargetFeedConsumption());
                }
            }
            $entity->setMortalityPercent($entity->calculateMortalityPercent());
            $entity->setFeedConsumptionPerBird($entity->calculatePerBird());
            $entity->setFcrWithoutMortality($entity->calculateWithoutMortality());
            $entity->setFcrWithMortality($entity->calculateWithMortality());


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }


        return $this->render('@TerminalbdCrm/fcr/details-modal.html.twig', [
            'customerId' => $customer?$customer->getId():null,
            'customer' => $customer?$customer:null,
            'form' => $form->createView(),
            'report'=>$report,
            'fcrDetails'=>$fcrAllReports,
            'employee'=>$this->getUser(),
            'fcrOfFeed'=>strtoupper($afterBefore),
        ]);
    }


    /**
     * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER')")
     * @Route("/{report}/{afterBefore}/new", methods={"GET", "POST"}, name="fcr_after_new", options={"expose"=true})
     * @param Request $request
     * @param Setting $report
     * @param $afterBefore
     * @return Response
     * @throws \Exception
     */
    public function newAfter(Request $request, Setting $report, $afterBefore): Response
    {
        $fcrAllReports = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReportByReportingDateAndFeedType($afterBefore, $report, $this->getUser());
        $data = $request->request->get('fcr_details_for_after_form');
        $agent = null;
            $entity = new FcrDetails();

            $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
            $form = $this->createForm(FcrDetailsForAfterFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo, 'report' => $report));
            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(isset($data['agent'])&&$data['agent']!=''){
                $agent = $this->getDoctrine()->getRepository(Agent::class)->find($data['agent']);
            }

            $reportingDate = date('Y-m-d',strtotime('now'));
            $entity->setReportingMonth(new \DateTime($reportingDate));
            $entity->setFcrOfFeed(strtoupper($afterBefore));
            $entity->setReport($report);
            $entity->setEmployee($this->getUser());

            $entity->setAgent($agent);


            if(in_array($report->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

                /* @var SonaliStandard $sonaliStandard*/
                $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
                if($sonaliStandard){
                    $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                    $entity->setFeedConsumptionStandard($sonaliStandard->getCumulativeFeedIntake());
                }
            }
            if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

                /* @var BroilerStandard $broilerStandard*/
                $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
                if($broilerStandard){
                    $entity->setWeightStandard($broilerStandard->getTargetBodyWeight());
                    $entity->setFeedConsumptionStandard($broilerStandard->getTargetFeedConsumption());
                }
            }
            $entity->setMortalityPercent($entity->calculateMortalityPercent());
            $entity->setFeedConsumptionPerBird($entity->calculatePerBird());
            $entity->setFcrWithoutMortality($entity->calculateWithoutMortality());
            $entity->setFcrWithMortality($entity->calculateWithMortality());


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }

            return $this->render('@TerminalbdCrm/fcr/details-modal-after.html.twig', [
                'report'=>$report,
                'fcrDetails'=>$fcrAllReports,
                'fcrOfFeed'=>strtoupper($afterBefore),
                'form' => $form->createView(),
                'employee'=>$this->getUser(),
            ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="fcr_detail_delete")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(FcrDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    /**
     * @param Setting $report
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/{afterBefore}/details/refresh", methods={"GET", "POST"}, name="fcr_details_refresh", options={"expose"=true})
     */
    public function fcrDetailsRefresh(Request $request, Setting $report, $afterBefore): Response
    {
        $fcrAllReports = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrReportByReportingDateAndFeedType($afterBefore, $report, $this->getUser());


        return $this->render('@TerminalbdCrm/fcr/partial/fcr-details.html.twig', [
            'fcrDetails' => $fcrAllReports,
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/sonali-broiler/standard", methods={"POST"}, name="crm_sonali_and_broiler_standard_by_age", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function getSonaliBroilerStandardUsingAjax(Request $request, Setting $report): Response
    {
        $ageDay = $request->request->get('ageDay');

        $returnData = array();

        if(in_array($report->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

            /* @var SonaliStandard $sonaliStandard*/
            $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$ageDay));
            if($sonaliStandard){
                $returnData = array(
                    'status'=>200,
                    'weightStandard'=> $sonaliStandard->getTargetBodyWeight(),
                    'feedConsumptionStandard'=> $sonaliStandard->getCumulativeFeedIntake(),

                );
            }else{
                $returnData = array(
                    'status'=>404,
                );
            }
        }
        if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

            /* @var BroilerStandard $broilerStandard*/
            $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$ageDay));
            if($broilerStandard){
                $returnData = array(
                    'status'=>200,
                    'weightStandard'=> $broilerStandard->getTargetBodyWeight(),
                    'feedConsumptionStandard'=> $broilerStandard->getTargetFeedConsumption(),

                );
            }else{
                $returnData = array(
                    'status'=>404,
                );
            }
        }

        return new JsonResponse($returnData);

    }



}
