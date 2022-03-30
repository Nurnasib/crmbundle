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
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Form\ChickLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;


/**
 * @Route("/crm/chick")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER')")
 */
class ChickLifeCycleController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", methods={"GET"}, name="crm_chick")
     */
    public function index( ): Response
    {

        $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/chickLifecycle/index.html.twig',['entities' => $entities]);
    }

    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @return Response
     * @throws \Exception
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="chick_new_modal")
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
//        var_dump(date('Y-m-d',strtotime('now')));die;
        $entity = new ChickLifeCycle();
        $existReport = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findOneBy(array('employee'=>$this->getUser(), 'customer'=>$crmCustomer, 'report'=>$report, 'lifeCycleState'=>ChickLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS));
        if ($existReport){
            $entity= $existReport;
        }

        if($existReport){
            return $this->redirectToRoute('chick_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }

        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser(), 'report' => $report))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportingDate = date('Y-m-d',strtotime('now'));

            $entity->setReportingDate(new \DateTime($reportingDate));

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
                return new Response('success');
//                return $this->redirectToRoute('chick_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
            }
            return new Response('success');
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
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="chick_life_cycle_details_modal")
     * @return Response
     */
    public function lifeCycleDetailsModal(ChickLifeCycle $chickLifeCycle): Response
    {
        $lifeCycleSetting = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findOneBy(array('report'=>$chickLifeCycle->getReport()));
        $crmChickLifeCycleDetails = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->findOneBy(array('crmChickLifeCycle'=>$chickLifeCycle->getId()));
        if (!$crmChickLifeCycleDetails){
            for($i=1; $i<=$lifeCycleSetting->getNumberOfWeek(); $i++){
                $chickLifeCycleDetails = new ChickLifeCycleDetails();

                $chickLifeCycleDetails->setVisitingWeek($i);
//                $chickLifeCycleDetails->setTotalBirds($chickLifeCycle->getTotalBirds());
                $chickLifeCycleDetails->setCrmChickLifeCycle($chickLifeCycle);
                $chickLifeCycleDetails->setCreatedAt(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($chickLifeCycleDetails);

                $em->flush();
            }
        }

        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1, 'settingType'=>'FEED_TYPE','parent'=>$chickLifeCycle->getReport()->getParent()),['name' => 'ASC']);

        return $this->render('@TerminalbdCrm/chickLifecycle/chick-life-cycle-modal.html.twig', [
            'chickLifeCycle' => $chickLifeCycle,
            'feedTypes' => $feedTypes,
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/life-cycle/{id}/edit", methods={"POST"}, name="crm_chick_life_cycle_edit", options={"expose"=true})
     * @param Request $request
     * @param ChickLifeCycleDetails $entity
     * @return Response
     * @throws \Exception
     */

    public function editLifeCycleDetails(Request $request, ChickLifeCycleDetails $entity): Response
    {
        $data = $request->request->all();
        $hatchery = null;
        $breed = null;
        $feed= null;
        $feedType= null;

//        $entity->setTotalBirds(isset($data['totalBirds'])?$data['totalBirds']:0);
        $entity->setAgeDays(isset($data['ageDays'])?$data['ageDays']:0);
        $entity->setMortalityPes(isset($data['mortalityPes'])?$data['mortalityPes']:0);
        $entity->setMortalityPercent($entity->calculateMortalityPercent());
        $entity->setWeightAchieved(isset($data['weightAchieved'])?$data['weightAchieved']:0);
        $entity->setFeedTotalKg(isset($data['feedTotalKg'])?$data['feedTotalKg']:0);
        $entity->setPerBird($entity->calculatePerBird());
        $entity->setWithoutMortality($entity->calculateWithoutMortality());
        $entity->setWithMortality($entity->calculateWithMortality());

//        $entity->setHatchery($hatchery);
//        $entity->setBreed($breed);
//        $entity->setFeed($feed);
        $entity->setFeedType($feedType);

        $reportingDate = isset($data['reportingDate'])&&$data['reportingDate']!=""?date('Y-m-d',strtotime($data['reportingDate'])):date('Y-m-d',strtotime('now'));
        $entity->setReportingDate(new \DateTime($reportingDate));

        $currentTime = date('H:i:s',strtotime('now'));
        $proDate = isset($data['proDate'])&&$data['proDate']!=""?date('Y-m-d',strtotime($data['proDate'])):date('Y-m-d',strtotime('now'));
        $proDate = $proDate.' '.$currentTime;
        $entity->setProDate(new \DateTime($proDate));
        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:null);
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:null);
        $entity->setWeightStandard(0);
        $entity->setFeedStandard(0);
        if($entity->getCrmChickLifeCycle()->getReport()->getSlug()=='sonali-life-cycle'){

            /* @var SonaliStandard $sonaliStandard*/
            $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$entity->getAgeDays()));
            if($sonaliStandard){
                $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                $entity->setFeedStandard($sonaliStandard->getCumulativeFeedIntake());
            }
        }
        if($entity->getCrmChickLifeCycle()->getReport()->getSlug()=='boiler-life-cycle'){

            /* @var BroilerStandard $broilerStandard*/
            $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$entity->getAgeDays()));
            if($broilerStandard){
                $entity->setWeightStandard($broilerStandard->getTargetBodyWeight());
                $entity->setFeedStandard($broilerStandard->getTargetFeedConsumption());
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'mortalityPercent'=>$entity->getMortalityPercent(),
                'weightStandard'=>$entity->getWeightStandard(),
                'feedStandard'=>$entity->getFeedStandard(),
                'perBird'=>$entity->getPerBird(),
                'withoutMortality'=>$entity->getWithoutMortality(),
                'withMortality'=>$entity->getWithMortality(),
                'data'=>$data,
                'status'=>200,
            )
        );

    }

    /**
     * Deletes a ChickLifeCycle entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_chick_delete")
     * @param $id
     * @return Response
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

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/life-cycle/{id}/complete", methods={"POST"}, name="crm_chick_life_cycle_complete", options={"expose"=true})
     * @return Response
     */
    public function chickLifeCycleReportComplete(ChickLifeCycle $chickLifeCycle): Response
    {
        $chickLifeCycle->setLifeCycleState(ChickLifeCycle::LIFE_CYCLE_STATE_COMPLETE);
        $em = $this->getDoctrine()->getManager();
        $em->persist($chickLifeCycle);
        $em->flush();
        return new JsonResponse(array(
            'message'=>"Success",
            'status'=>200
        ));
    }


    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report", methods={"GET"}, name="crm_chick_report_detail")
     * @return Response
     */
    public function reportDetails( ChickLifeCycle $chickLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/chickLifecycle/report/report-details.html.twig',['chickLifeCycle' => $chickLifeCycle]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report/pdf", methods={"GET"}, name="crm_chick_report_detail_pdf")
     * @return Response
     */
    public function reportPdf( ChickLifeCycle $chickLifeCycle): Response
    {

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial, sans-serif');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/chickLifecycle/report/report-pdf.html.twig',['chickLifeCycle' => $chickLifeCycle]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($chickLifeCycle->getReport()->getSlug().".pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report/excel", methods={"GET"}, name="crm_chick_report_detail_excel")
     * @return Response
     */
    public function reportExcel( ChickLifeCycle $chickLifeCycle): Response
    {
        $html = $this->renderView('@TerminalbdCrm/chickLifecycle/report/report-excel.html.twig',['chickLifeCycle' => $chickLifeCycle]);

        $file=$chickLifeCycle->getReport()->getSlug().'_'.time().".xls";
        $test="$html";
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$file");
        echo $test;die;

    }


}
