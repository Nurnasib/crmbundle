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
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetailSpecies;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\FishLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/fish/life/cycle")
 * @Security("is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_DEVELOPER)")
 */
class FishLifeCycleController extends AbstractController
{

    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @param $afterBefore
     * @return Response
     * @throws \Exception
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Route("/customer/{id}/report/{report}/{afterBefore}/new/modal", methods={"GET", "POST"}, name="fish_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report, $afterBefore): Response
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
                        ->andWhere("e.status=1")
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
                $existReport = $this->getDoctrine()->getRepository(FishLifeCycle::class)->findOneBy(array('reportingMonth'=>$date, 'report'=>$report, 'customer'=>$crmCustomer, 'employee'=>$this->getUser(), 'reportType'=>$afterBefore));

                if($existReport){
                    $entity=$existReport;
                }

                $entity->setReportingMonth(new \DateTime($reportingDate));
                $entity->setCustomer($crmCustomer);
                $entity->setReport($report);
                $entity->setEmployee($this->getUser());
                if($afterBefore=='before'){
                    $entity->setReportType(FishLifeCycle::REPORT_TYPE_BEFORE);
                }
                if($afterBefore=='after'){
                    $entity->setReportType(FishLifeCycle::REPORT_TYPE_AFTER);
                }

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
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="fish_life_cycle_details_modal", options={"expose"=true})
     * @param FishLifeCycle $fishLifeCycle
     * @return Response
     */
    public function lifeCycleDetailsModal( FishLifeCycle $fishLifeCycle): Response
    {
        $feedCompanies = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFeedCompanyByFishLifeCycle($fishLifeCycle);
        $fishLifeCycleDetails = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByFishLifeCycle($fishLifeCycle);
        $fishLifeCycleDetailsByReportingMonth = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByReportingDateAndEmployee($fishLifeCycle->getReport(), $this->getUser());
        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FEED_TYPE','parent'=>$fishLifeCycle->getReport()->getParent()),['name' => 'ASC']);
        $mainCultureSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$fishLifeCycle->getReport()->getParent()->getParent()),['name' => 'ASC']);
        $hatcheries = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'HATCHERY'),['name' => 'ASC']);

        if($fishLifeCycle->getReportType()==FishLifeCycle::REPORT_TYPE_AFTER){
            return $this->render('@TerminalbdCrm/fishLifeCycle/fish-life-cycle-after-sale-details-report-modal.html.twig', [
                'fishLifeCycle' => $fishLifeCycle,
                'fishLifeCycleDetails' => $fishLifeCycleDetails,
                'feedCompanies' => $feedCompanies,
                'feedTypes' => $feedTypes,
                'mainCultureSpecies' => $mainCultureSpecies,
                'hatcheries' => $hatcheries,
                'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
            ]);
        }

        return $this->render('@TerminalbdCrm/fishLifeCycle/fish-life-cycle-details-report-modal.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'fishLifeCycleDetails' => $fishLifeCycleDetails,
            'feedCompanies' => $feedCompanies,
            'feedTypes' => $feedTypes,
            'mainCultureSpecies' => $mainCultureSpecies,
            'hatcheries' => $hatcheries,
            'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
        ]);
    }


    /**
     * @Route("/details/{id}/update", methods={"POST"}, name="fish_life_cycle_details_data_update", options={"expose"=true})
     * @param Request $request
     * @param FishLifeCycleDetails $entity
     * @return Response
     * @throws \Exception
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



            if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_BEFORE){
                $entity->setFinalWeightGm($entity->calculateFinalWeightGm());
                $entity->setTotalDayOfCulture($entity->calculateTotalDayOfCulture());
                $entity->setFinalFcr($entity->calculateFinalFcr());
                $entity->setFinalAdg($entity->calculateFinalAdg());
            }
            if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_AFTER){
                $entity->setTotalDayOfCulture($entity->calculateDayOfCultureForAfterSale());
                $entity->setFinalFcr($entity->calculateFinalFcrForAfterSale());
                $entity->setFinalAdg($entity->calculateFinalAdgForAfterSale());
            }
            $entity->setFinalWeightKg($entity->calculateFinalWeightKg());
            $entity->setTotalFeedConsumptionKg($entity->calculateTotalFeedConsumptionKg());

            if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_AFTER){
                $entity->setTotalSeedCost($entity->calculateTotalSeedCost());
                $entity->setTotalFeedCost($entity->calculateTotalFeedCost());
                $entity->setFeedCostPerKgFish($entity->calculateFeedCostPerKgFish());
                $entity->setTotalCost($entity->calculateTotalCost());
                $entity->setProductionCostPerKgFish($entity->calculateProductionCostPerKgFish());
                $entity->setTotalIncome($entity->calculateTotalIncome());
                $entity->setNetProfitOrLoss($entity->calculateNetProfitOrLoss());
                $entity->setRetuneOverInvestment($entity->calculateRetuneOverInvestment());
            }

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
                'feedType'=>$entity->getFeedType(),
                'mainCultureSpecieses'=>$entity->getMainCultureSpecies(),
                'totalInitialWeight'=>$entity->getTotalInitialWeight(),
                'currentCultureDays'=>$entity->getCurrentCultureDays(),
                'weightGainKg'=>$entity->getWeightGainKg(),
                'currentFcr'=>$entity->getCurrentFcr(),
                'currentAdg'=>$entity->getCurrentAdg(),
                'finalWeightGm'=>$entity->getFinalWeightGm(),
                'finalWeightKg'=>$entity->getFinalWeightKg(),
                'totalDayOfCulture'=>$entity->getTotalDayOfCulture(),
                'currentFeedConsumptionKg'=>$entity->getCurrentFeedConsumptionKg(),
                'totalFeedConsumptionKg'=>$entity->getTotalFeedConsumptionKg(),
                'finalFcr'=>$entity->getFinalFcr(),
                'finalAdg'=>$entity->getFinalAdg(),
                'srPercentage'=>$entity->getSrPercentage(),
                'totalSeedCost'=>$entity->getTotalSeedCost(),
                'totalFeedCost'=>$entity->getTotalFeedCost(),
                'feedCostPerKgFish'=>number_format($entity->getFeedCostPerKgFish(),0,'.',''),
                'totalCost'=>number_format($entity->getTotalCost(),0,'.',''),
                'productionCostPerKgFish'=>number_format($entity->getProductionCostPerKgFish(),0,'.',''),
                'totalIncome'=>number_format($entity->getTotalIncome(),0,'.',','),
                'netProfitOrLoss'=>number_format($entity->getNetProfitOrLoss(),0,'.',','),
                'retuneOverInvestment'=>number_format($entity->getRetuneOverInvestment(),2,'.',''),
                'status'=>200,
            )
        );

    }

    /**
     * @Route("/detail/{id}/species/insert", methods={"POST"}, name="fish_life_cycle_detail_species_data_insert", options={"expose"=true})
     * @param Request $request
     * @param FishLifeCycleDetails $entity
     * @return Response
     */

    public function insertLifeCycleDetailSpecies(Request $request, FishLifeCycleDetails $entity): Response
    {
        $data = $request->request->all();
        $feedTypeId = $data['feedType'];
        $main_culture_speciesId = $data['main_culture_species'];
        $feed_consumption_kg = $data['feed_consumption_kg'];


        if($feedTypeId!=''&&$main_culture_speciesId!=''&&$feed_consumption_kg!=''){

            $feedType= $this->getDoctrine()->getRepository(Setting::class)->find($feedTypeId);
            $cultureSpecies= $this->getDoctrine()->getRepository(Setting::class)->find($main_culture_speciesId);

            $existingDetailsSpecies= $this->getDoctrine()->getRepository(FishLifeCycleDetailSpecies::class)->findOneBy(array('feedType'=>$feedType, 'fishLifeCycleDetails'=>$entity));

            $fishLifeCycleDetailSpecies = new FishLifeCycleDetailSpecies();

            if($existingDetailsSpecies){
                $fishLifeCycleDetailSpecies=$existingDetailsSpecies;
            }

            $fishLifeCycleDetailSpecies->setMainCultureSpecies($cultureSpecies?$cultureSpecies:null);
            $fishLifeCycleDetailSpecies->setFeedType($feedType?$feedType:null);
            $fishLifeCycleDetailSpecies->setFeedConsumptionKg($feed_consumption_kg?$feed_consumption_kg:0);
            $fishLifeCycleDetailSpecies->setFishLifeCycleDetails($entity);
            $em = $this->getDoctrine()->getManager();
            $em->persist($fishLifeCycleDetailSpecies);
            $em->flush();

        }

        $entity->setCurrentFeedConsumptionKg($entity->getSpeciesFeedConsumptionKg());

        $entity->setTotalInitialWeight($entity->calculateTotalInitialWeight());
        $entity->setCurrentCultureDays($entity->calculateCurrentCultureDays());

//            $entity->setWeightGainGm($entity->calculateWeightGainGm());
        $entity->setWeightGainKg($entity->calculateWeightGainKg());

        $entity->setCurrentFcr($entity->calculateCurrentFcr());
        $entity->setCurrentAdg($entity->calculateCurrentAdg());



        if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_BEFORE){
            $entity->setFinalWeightGm($entity->calculateFinalWeightGm());
            $entity->setTotalDayOfCulture($entity->calculateTotalDayOfCulture());
            $entity->setFinalFcr($entity->calculateFinalFcr());
            $entity->setFinalAdg($entity->calculateFinalAdg());
        }
        if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_AFTER){
            $entity->setTotalDayOfCulture($entity->calculateDayOfCultureForAfterSale());
            $entity->setFinalFcr($entity->calculateFinalFcrForAfterSale());
            $entity->setFinalAdg($entity->calculateFinalAdgForAfterSale());
        }
        $entity->setFinalWeightKg($entity->calculateFinalWeightKg());
        $entity->setTotalFeedConsumptionKg($entity->calculateTotalFeedConsumptionKg());

        if(strtoupper($entity->getFishLifeCycle()->getReportType())==FishLifeCycle::REPORT_TYPE_AFTER){
            $entity->setTotalSeedCost($entity->calculateTotalSeedCost());
            $entity->setTotalFeedCost($entity->calculateTotalFeedCost());
            $entity->setFeedCostPerKgFish($entity->calculateFeedCostPerKgFish());
            $entity->setTotalCost($entity->calculateTotalCost());
            $entity->setProductionCostPerKgFish($entity->calculateProductionCostPerKgFish());
            $entity->setTotalIncome($entity->calculateTotalIncome());
            $entity->setNetProfitOrLoss($entity->calculateNetProfitOrLoss());
            $entity->setRetuneOverInvestment($entity->calculateRetuneOverInvestment());
        }

        $entity->setSrPercentage($entity->calculateSrPercentage());
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        
        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'id'=>$entity->getId(),
                'feedType'=>$entity->getFeedType(),
                'mainCultureSpecieses'=>$entity->getMainCultureSpecies(),
                'totalInitialWeight'=>$entity->getTotalInitialWeight(),
                'currentCultureDays'=>$entity->getCurrentCultureDays(),
                'weightGainKg'=>$entity->getWeightGainKg(),
                'currentFcr'=>$entity->getCurrentFcr(),
                'currentAdg'=>$entity->getCurrentAdg(),
                'finalWeightGm'=>$entity->getFinalWeightGm(),
                'finalWeightKg'=>$entity->getFinalWeightKg(),
                'totalDayOfCulture'=>$entity->getTotalDayOfCulture(),
                'currentFeedConsumptionKg'=>$entity->getCurrentFeedConsumptionKg(),
                'totalFeedConsumptionKg'=>$entity->getTotalFeedConsumptionKg(),
                'finalFcr'=>$entity->getFinalFcr(),
                'finalAdg'=>$entity->getFinalAdg(),
                'srPercentage'=>$entity->getSrPercentage(),
                'totalSeedCost'=>$entity->getTotalSeedCost(),
                'totalFeedCost'=>$entity->getTotalFeedCost(),
                'feedCostPerKgFish'=>number_format($entity->getFeedCostPerKgFish(),0,'.',''),
                'totalCost'=>number_format($entity->getTotalCost(),0,'.',''),
                'productionCostPerKgFish'=>number_format($entity->getProductionCostPerKgFish(),0,'.',''),
                'totalIncome'=>number_format($entity->getTotalIncome(),0,'.',','),
                'netProfitOrLoss'=>number_format($entity->getNetProfitOrLoss(),0,'.',','),
                'retuneOverInvestment'=>number_format($entity->getRetuneOverInvestment(),2,'.',''),
                'status'=>200,
            )
        );

    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/{id}/refresh", methods={"GET"}, name="crm_fish_life_cycle_refresh", options={"expose"=true})
     * @return Response
     */
    public function fishLifeCycleReportRefresh(FishLifeCycle $fishLifeCycle): Response
    {
        $fishLifeCycleDetailsByReportingMonth = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByReportingDateAndEmployee($fishLifeCycle->getReport(), $this->getUser());
        if($fishLifeCycle->getReportType()==FishLifeCycle::REPORT_TYPE_AFTER){
            return $this->render('@TerminalbdCrm/fishLifeCycle/partial/fish-life-cycle-after-sale-details-body.html.twig', [
                'fishLifeCycle' => $fishLifeCycle,
                'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
            ]);
        }
        return $this->render('@TerminalbdCrm/fishLifeCycle/partial/fish-life-cycle-details-body.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/details/{id}/delete", methods={"POST"}, name="fish_life_cycle_detail_delete", options={"expose"=true})
     * @param $id
     * @return Response
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
     * @return Response
     */
    public function indexReport( string $report): Response
    {

        $entities = $this->getDoctrine()->getRepository(FishLifeCycle::class)->getFishLifeCycleByReportType($report);
        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report.html.twig',['entities' => $entities]);
    }

    /**
     * @param FishLifeCycle $cattleLifeCycle
     * @Route("/life/cycle/{id}/report", methods={"GET"}, name="crm_fish_report_detail")
     * @return Response
     */
    public function reportDetails( FishLifeCycle $cattleLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/cattleLifecycle/report/report-details.html.twig',['cattleLifeCycle' => $cattleLifeCycle]);
    }

    /**
     * @param $id
     * @return Response
     * @Route("/species/parent/{id}", methods={"GET"}, name="crm_main_culture_species_name", options={"expose"=true})
     */
    public function mainCultureSpecies($id): Response
    {
        $mainCultureSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_NAME','parent'=>$id),['name' => 'ASC']);
        $returnArray=[];
        if($mainCultureSpecies){
            foreach ($mainCultureSpecies as $mainCultureSpecie){
                $returnArray[]= array('id'=>$mainCultureSpecie->getId(), 'name'=>$mainCultureSpecie->getName());
            }
        }
        
        return new JsonResponse($returnArray);
    }


}
