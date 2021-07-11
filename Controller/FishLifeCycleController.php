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
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $em = $this->getDoctrine()->getManager();
        /*$existReport = $this->getDoctrine()->getRepository(FishLifeCycle::class)->getFishReportByReportingDateAndFeedType($report, $crmCustomer, $this->getUser());

        if($existReport){
            return $this->redirectToRoute('fish_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }*/

        $form = $this->createFormBuilder()
            ->add('feed', EntityType::class, array(
                'required'    => true,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed',
                'choice_label' => 'name',
                'multiple'=>true,
                'attr'=>array('class'=>'span12 m-wrap feed'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='FEED_NAME'")
                        ->orderBy('e.name', 'ASC');
                },
            ))->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $data = $form->getData();
            if(isset($data['feed'])){

                $reportingDate = date('Y-m-d',strtotime('now'));
                $date=new \DateTime($reportingDate);

                $entity = new FishLifeCycle();
                $existReport = $this->getDoctrine()->getRepository(FishLifeCycle::class)->findOneBy(array('reportingMonth'=>$date, 'report'=>$report, 'customer'=>$crmCustomer, 'employee'=>$this->getUser()));

                if($existReport){
                    $entity=$existReport;
                }

                $entity->setReportingMonth(new \DateTime($reportingDate));
                $entity->setCustomer($crmCustomer);
                $entity->setReport($report);
                $entity->setEmployee($this->getUser());

                $em->persist($entity);

                foreach ($data['feed'] as $feed){
                    $fishLifeCycleDetails = new FishLifeCycleDetails();

                    $existDetails = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->findOneBy(array('fishLifeCycle'=>$entity, 'feed'=>$feed));
                    if($existDetails){
                        $fishLifeCycleDetails=$existDetails;
                    }
                    $fishLifeCycleDetails->setReportingDate(new \DateTime($reportingDate));
                    $fishLifeCycleDetails->setFeed($feed);
                    $fishLifeCycleDetails->setFishLifeCycle($entity);
                    $fishLifeCycleDetails->setCustomer($entity->getCustomer());
                    $fishLifeCycleDetails->setAgent($entity->getCustomer()->getAgent());
                    $em->persist($fishLifeCycleDetails);
                }

                $em->flush();
            }

            $this->addFlash('success', 'post.created_successfully');

            return $this->redirectToRoute('fish_life_cycle_details_modal', ['id'=>$entity->getId()]);

        }


        return $this->render('@TerminalbdCrm/fishLifeCycle/fish-life-cycle.html.twig', [
            'customer' => $crmCustomer,
            'report' => $report,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="fish_life_cycle_details_modal", options={"expose"=true})
     */
    public function lifeCycleDetailsModal( FishLifeCycle $fishLifeCycle): Response
    {
        $feedCompanies = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFeedCompanyByFishLifeCycle($fishLifeCycle);
        $fishLifeCycleDetails = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByFishLifeCycle($fishLifeCycle);
        $fishLifeCycleDetailsByReportingMonth = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByReportingDateAndEmployee($fishLifeCycle->getReport(), $this->getUser());
        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FEED_TYPE','parent'=>$fishLifeCycle->getReport()->getParent()),['name' => 'ASC']);
        $mainCultureSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$fishLifeCycle->getReport()->getParent()->getParent()),['name' => 'ASC']);
        $hatcheries = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'HATCHERY'),['name' => 'ASC']);


        return $this->render('@TerminalbdCrm/fishLifeCycle/fish-life-cycle-details-report-modal.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'fishLifeCycleDetails' => $fishLifeCycleDetails,
            'feedCompanies' => $feedCompanies,
            'feedTypes' => $feedTypes,
            'mainCultureSpecies' => $mainCultureSpecies,
            'hatcheries' => $hatcheries,
            'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
//            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/details/{id}/update", methods={"POST"}, name="fish_life_cycle_details_data_update", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function editLifeCycleDetails(Request $request, FishLifeCycleDetails $entity): Response
    {
        $data = $request->request->all();
        $metaKey = $data['dataMetaKey'];
        $metaValue = $data['dataMetaValue'];
        $inputType = $data['dataInputType'];


        if($metaKey!=''&&$metaValue!=''){

            if($inputType=='datetime'){
                $metaValue= isset($metaValue)&&$metaValue!=""?date('Y-m-d',strtotime($metaValue)):date('Y-m-d',strtotime('now'));
                $metaValue = new \DateTime($metaValue);
            }

            if($inputType=='number'){
                $metaValue = $metaValue>0?$metaValue:0;
            }

            if($inputType=='select'){
                $metaValue = $this->getDoctrine()->getRepository(Setting::class)->find($metaValue);
            }

            $set = 'set'.$metaKey;

            $entity->$set($metaValue);

            $entity->setTotalInitialWeight($entity->calculateTotalInitialWeight());
            $entity->setCurrentCultureDays($entity->calculateCurrentCultureDays());

//            $entity->setWeightGainGm($entity->calculateWeightGainGm());
            $entity->setWeightGainKg($entity->calculateWeightGainKg());

            $entity->setCurrentFcr($entity->calculateCurrentFcr());
            $entity->setCurrentAdg($entity->calculateCurrentAdg());

            $entity->setFinalWeightGm($entity->calculateFinalWeightGm());
            $entity->setFinalWeightKg($entity->calculateFinalWeightKg());
            $entity->setTotalDayOfCulture($entity->calculateTotalDayOfCulture());
            $entity->setTotalFeedConsumptionKg($entity->calculateTotalFeedConsumptionKg());
            $entity->setFinalFcr($entity->calculateFinalFcr());
            $entity->setFinalAdg($entity->calculateFinalAdg());
            $entity->setSrPercentage($entity->calculateSrPercentage());

        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'id'=>$entity->getId(),
                'totalInitialWeight'=>$entity->getTotalInitialWeight(),
                'currentCultureDays'=>$entity->getCurrentCultureDays(),
                'weightGainKg'=>$entity->getWeightGainKg(),
                'currentFcr'=>$entity->getCurrentFcr(),
                'currentAdg'=>$entity->getCurrentAdg(),
                'finalWeightGm'=>$entity->getFinalWeightGm(),
                'finalWeightKg'=>$entity->getFinalWeightKg(),
                'totalDayOfCulture'=>$entity->getTotalDayOfCulture(),
                'totalFeedConsumptionKg'=>$entity->getTotalFeedConsumptionKg(),
                'finalFcr'=>$entity->getFinalFcr(),
                'finalAdg'=>$entity->getFinalAdg(),
                'srPercentage'=>$entity->getSrPercentage(),
                'status'=>200,
            )
        );

    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/{id}/refresh", methods={"GET"}, name="crm_fish_life_cycle_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function fishLifeCycleReportRefresh(FishLifeCycle $fishLifeCycle): Response
    {
        $fishLifeCycleDetailsByReportingMonth = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByReportingDateAndEmployee($fishLifeCycle->getReport(), $this->getUser());

        return $this->render('@TerminalbdCrm/fishLifeCycle/partial/fish-life-cycle-details-body.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
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
