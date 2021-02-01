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
use Terminalbd\CrmBundle\Entity\Fcr;
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
     * @Route("/", methods={"GET"}, name="fcr")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Fcr::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/fcr/index.html.twig',['entities' => $entities]);
    }

//    /**
//     * @Route("/report", methods={"GET"}, name="fcr_report")
//     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
//     */
/*    public function indexReport(Request $request): Response
    {

        $entities = $this->getDoctrine()->getRepository(Fcr::class)->findAll();
        return $this->render('@TerminalbdCrm/fcr/report.html.twig',[
            'entities' => $entities,
        ]);
    }*/

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
     * @Route("/{customer}/{report}/{afterBefore}/new", methods={"GET", "POST"}, name="fcr_new")
     */
    public function new(Request $request, CrmCustomer $customer, Setting $report, $afterBefore): Response
    {
        $existingReport = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReportByReportingDateAndFeedType($afterBefore, $report, $this->getUser());
//        var_dump($existingReport);die;
        if($existingReport){
            return $this->redirectToRoute('fcr_details_modal', ['id'=>$existingReport->getId(), 'customer'=>$customer->getId()]);
        }
        $entity = new Fcr();
        $reportingDate = date('Y-m-d',strtotime('now'));
        $entity->setReportingMonth(new \DateTime($reportingDate));
        $entity->setFcrOfFeed(strtoupper($afterBefore));
        $entity->setReport($report);
        $entity->setEmployee($this->getUser());
//        $entity->setCustomer($customer);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        return $this->redirectToRoute('fcr_details_modal', ['id'=>$entity->getId(), 'customer'=>$customer->getId()]);
//        return new Response('success');
    }


    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{report}/{afterBefore}/new", methods={"GET", "POST"}, name="fcr_after_new")
     */
    public function newAfter(Request $request, Setting $report, $afterBefore): Response
    {
        $existingReport = $this->getDoctrine()->getRepository(Fcr::class)->getFcrReportByReportingDateReportAndEmployeeForAfter($afterBefore, $report, $this->getUser());

        if($existingReport){
            return $this->redirectToRoute('fcr_details_modal', ['id'=>$existingReport->getId()]);
        }
        $entity = new Fcr();
        $reportingDate = date('Y-m-d',strtotime('now'));
        $entity->setReportingMonth(new \DateTime($reportingDate));
        $entity->setFcrOfFeed(strtoupper($afterBefore));
        $entity->setReport($report);
        $entity->setEmployee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        return $this->redirectToRoute('fcr_details_modal', ['id'=>$entity->getId()]);
//        return new Response('success');
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
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="fcr_detail_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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
     * @param Fcr $fcr
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/modal", methods={"GET", "POST"}, name="fcr_details_modal")
     */
    public function newModal(Request $request, Fcr $fcr): Response
    {
//        $data = $request->request->get('fcr_details_form');
//        $agents=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($fcr->getEmployee());
        $entity = new FcrDetails();

        if ($fcr->getFcrOfFeed()==='AFTER'){
            $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
            $form = $this->createForm(FcrDetailsForAfterFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo, 'report' => $fcr->getReport()))
                ->add('SaveAndCreate', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-sm')));
            $form->handleRequest($request);

            return $this->render('@TerminalbdCrm/fcr/details-modal-after.html.twig', [
                'entity' => $fcr,
//            'agents' => $agents,
                'form' => $form->createView(),
            ]);
        }


        $form = $this->createForm(FcrDetailsFormType::class, $entity,array('user' => $this->getUser(), 'report' => $fcr->getReport()))
            ->add('SaveAndCreate', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-sm')));
        $form->handleRequest($request);

        return $this->render('@TerminalbdCrm/fcr/details-modal.html.twig', [
            'entity' => $fcr,
//            'agents' => $agents,
            'customer' => $request->query->get('customer'),
            'form' => $form->createView(),
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
        $customer = null;
        $agent = null;
        $hatchery = null;
        $breed = null;
        $feed =null;
        $feedType= null;
        $feedMill = null;
        if(isset($data['hatchery'])&&$data['hatchery']!=''){
            $hatchery = $this->getDoctrine()->getRepository(Setting::class)->find($data['hatchery']);
        }
        if(isset($data['customerId'])&&$data['customerId']!=''){
            $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['customerId']);
        }
        if(isset($data['agent'])&&$data['agent']!=''){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($data['agent']);
        }
        if(isset($data['breed'])&&$data['breed']!=''){
            $breed = $this->getDoctrine()->getRepository(Setting::class)->find($data['breed']);
        }
        if(isset($data['feed'])&&$data['feed']!=''){
            $feed = $this->getDoctrine()->getRepository(Setting::class)->find($data['feed']);
        }
        if(isset($data['feedMill'])&&$data['feedMill']!=''){
            $feedMill = $this->getDoctrine()->getRepository(Setting::class)->find($data['feedMill']);
        }
        if(isset($data['feedType'])&&$data['feedType']!=''){
            $feedType = $this->getDoctrine()->getRepository(Setting::class)->find($data['feedType']);
        }

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

        $hatchingDate = isset($data['hatchingDate'])&&$data['hatchingDate']!=""?date('Y-m-d',strtotime($data['hatchingDate'])):date('Y-m-d',strtotime('now'));
        $entity->setHatchingDate(new \DateTime($hatchingDate));
        $proDate = isset($data['proDate'])&&$data['proDate']!=""?date('Y-m-d',strtotime($data['proDate'])):date('Y-m-d',strtotime('now'));
        $entity->setProDate(new \DateTime($proDate));

        $entity->setHatchery($hatchery);
        $entity->setBreed($breed);
        $entity->setFeed($feed);
        $entity->setFeedMill($feedMill);
        $entity->setFeedType($feedType);

        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:'');
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:'');
        if($fcr->getFcrOfFeed()=='AFTER'){
            $entity->setAgent($agent);
        }else{
            $entity->setCustomer($customer);
            $entity->setAgent($customer?$customer->getAgent():null);
        }

        $entity->setFcr($fcr);


        if(in_array($entity->getFcr()->getReport()->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

            /* @var SonaliStandard $sonaliStandard*/
            $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
            if($sonaliStandard){
                $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                $entity->setFeedConsumptionStandard($sonaliStandard->getFeedIntakePerDay());
            }
        }
        if(in_array($entity->getFcr()->getReport()->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

            /* @var BroilerStandard $broilerStandard*/
            $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
            if($broilerStandard){
                $entity->setWeightStandard($broilerStandard->getTargetBodyWeight());
                $entity->setFeedConsumptionStandard($broilerStandard->getTargetFeedConsumption());
            }
        }

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

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/sonali-broiler/standard", methods={"POST"}, name="crm_sonali_and_broiler_standard_by_age", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function getSonaliBroilerStandardUsingAjax(Request $request, Fcr $fcr): Response
    {
        $ageDay = $request->request->get('ageDay');

        $returnData = array();

        if(in_array($fcr->getReport()->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

            /* @var SonaliStandard $sonaliStandard*/
            $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$ageDay));
            if($sonaliStandard){
                $returnData = array(
                    'status'=>200,
                    'weightStandard'=> $sonaliStandard->getTargetBodyWeight(),
                    'feedConsumptionStandard'=> $sonaliStandard->getFeedIntakePerDay(),

                );
            }
        }
        if(in_array($fcr->getReport()->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

            /* @var BroilerStandard $broilerStandard*/
            $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$ageDay));
            if($broilerStandard){
                $returnData = array(
                    'status'=>200,
                    'weightStandard'=> $broilerStandard->getTargetBodyWeight(),
                    'feedConsumptionStandard'=> $broilerStandard->getTargetFeedConsumption(),

                );
            }
        }

        return new JsonResponse($returnData);

    }



}
