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
use Symfony\Component\Form\Extension\Core\DataTransformer\DateIntervalToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcr;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcrDetails;
use Terminalbd\CrmBundle\Entity\FishLifeCycle;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\CompanySpeciesWiseFcrAfterFormType;
use Terminalbd\CrmBundle\Form\CompanySpeciesWiseFcrFormType;
use Terminalbd\CrmBundle\Form\FishLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/fish/company/species")
 */
class FishCompanyAndSpeciesWiseAverageFcrController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="fish_company_species_wise_fcr_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CompanySpeciesWiseFcrFormType::class, null, array('report' => $report));

        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $data = $request->request->get('company_species_wise_fcr_form');
//            dd($data);
            $reportingDate = isset($data['reporting_date'])? $data['reporting_date'] : date('Y-m-d',strtotime('now'));
            $date=new \DateTime($reportingDate);
            if(isset($data['feed'])){
                $returnIds=[];
                $feedType = isset($data['feed_type'])?$this->getDoctrine()->getRepository(Setting::class)->find($data['feed_type']):null;
                foreach ($data['feed'] as $feedId){


                    $feed = $this->getDoctrine()->getRepository(Setting::class)->find($feedId);

                    $entity = new FishCompanyAndSpeciesWiseAverageFcr();
                    $existReport = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->findOneBy(array('reportingMonth'=>$date, 'report'=>$report, 'customer'=>$crmCustomer, 'employee'=>$this->getUser(),'feed'=>$feed,'feedType'=>$feedType));

                    if($existReport){
                        $entity=$existReport;
                    }

                    if($existReport){
                        $entity->setReportingMonth($entity->getReportingMonth());
                    }else{
                        $entity->setReportingMonth(new \DateTime($reportingDate));
                    }
                    $entity->setCustomer($crmCustomer);
                    $entity->setAgent($crmCustomer->getAgent()?$crmCustomer->getAgent():null);
                    $entity->setReport($report);
                    $entity->setEmployee($this->getUser());
                    $entity->setFeed($feed);
                    $entity->setFcrOfFeed('BEFORE');
                    $entity->setFeedType($feedType);
                    $em->persist($entity);

                    $speciesNameByFeedType= $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SPECIES_NAME','status'=>1,'parent'=>$feedType));
                    if($speciesNameByFeedType){
                        foreach ($speciesNameByFeedType as $item){
                            $fishCompanySpeciesWiseFcrDetails = new FishCompanyAndSpeciesWiseAverageFcrDetails();

                            $existDetails = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->findOneBy(array('speciesName'=>$item, 'fishCompanyAndSpeciesWiseAverageFcr'=>$entity));
                            if($existDetails){
                                $fishCompanySpeciesWiseFcrDetails=$existDetails;
                            }
                            $fishCompanySpeciesWiseFcrDetails->setSpeciesName($item);
                            $fishCompanySpeciesWiseFcrDetails->setFishCompanyAndSpeciesWiseAverageFcr($entity);
                            $em->persist($fishCompanySpeciesWiseFcrDetails);
                        }
                    }
                    $returnIds[]=$entity->getId();
                }

                $em->flush();
            }

            $this->addFlash('success', 'post.created_successfully');

            return $this->redirectToRoute('fish_company_species_wise_fcr_details', ['beforeAfter'=>"BEFORE",'feedType'=>$feedType->getId(),'ids'=>$returnIds,'created_date'=>$reportingDate]);

        }


        return $this->render('@TerminalbdCrm/fishCompanySpeciesWiseFcr/fish-company-species-wise-fcr.html.twig', [
            'customer' => $crmCustomer,
            'report' => $report,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{beforeAfter}/{feedType}/details", methods={"GET", "POST"}, name="fish_company_species_wise_fcr_details", options={"expose"=true})
     */
    public function companySpeciesWiseFcrDetails(Request $request, Setting $feedType, $beforeAfter): Response
    {
        $ids= $request->query->get('ids');
        $reportingDate = $request->query->get('created_date');

        $companySpeciesWiseFcrs = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->getFishCompanySpeciesWiseFcrByReportingDateAndIds($ids, $reportingDate, $this->getUser());

        $returnDetails=[];
        $mainCultureSpecies=null;
        if ($feedType){
            $mainCultureSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_NAME','parent'=>$feedType),['id' => 'ASC']);
        }

        if($companySpeciesWiseFcrs){
            /* @var FishCompanyAndSpeciesWiseAverageFcr $companySpeciesWiseFcr*/
            foreach ($companySpeciesWiseFcrs as $companySpeciesWiseFcr){
                /* @var FishCompanyAndSpeciesWiseAverageFcrDetails $andSpeciesWiseAverageFcrDetail*/
                foreach ($companySpeciesWiseFcr->getFishCompanyAndSpeciesWiseAverageFcrDetails() as $andSpeciesWiseAverageFcrDetail){
                    $returnDetails[$companySpeciesWiseFcr->getId()][$andSpeciesWiseAverageFcrDetail->getSpeciesName()->getId()] = $andSpeciesWiseAverageFcrDetail;
                }

            }
        }

        $fcrDetailsMonthWise = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getCompanySpeciesWiseFcrDetailsByReportingMonth($beforeAfter, $feedType->getId(), $reportingDate, $this->getUser()->getId());
        $fcrMonthWise = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->getFishCompanySpeciesWiseFcrByReportingMonth($beforeAfter, $feedType, $reportingDate, $this->getUser());

        return $this->render('@TerminalbdCrm/fishCompanySpeciesWiseFcr/fish-company-species-wise-fcr-details-report-modal.html.twig', [
            'companySpeciesWiseFcrs' => $companySpeciesWiseFcrs,
            'companySpeciesDetails' => $returnDetails,
            'mainCultureSpecies' => $mainCultureSpecies,
            'fcrDetailsMonthWise' => $fcrDetailsMonthWise,
            'fcrMonthWise' => $fcrMonthWise,
        ]);
    }


    /**
     * @Route("/details/{id}/update", methods={"POST"}, name="fish_company_species_wise_fcr_details_data_update", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function editCompanySpeciesWiseFcrDetails(Request $request, FishCompanyAndSpeciesWiseAverageFcrDetails $entity): Response
    {
        $data = $request->request->all();
        $metaValue = $data['dataMetaValue'];


        if($metaValue!=''){
            $entity->setQuantity($metaValue);

        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'id'=>$entity->getId(),
                'quantity'=>$entity->getQuantity(),
                'status'=>200,
            )
        );

    }

    /**
     * @param FishCompanyAndSpeciesWiseAverageFcr $companySpeciesWiseFcr
     * @Route("{id}/refresh", methods={"GET"}, name="crm_fish_company_species_wise_fcr_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function fishCompanySpeciesWiseFcrReportRefresh(Request $request, FishCompanyAndSpeciesWiseAverageFcr $companySpeciesWiseFcr)
    {
        $reportingDate = $companySpeciesWiseFcr->getReportingMonth()->format('Y-m-d');
        $mainCultureSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_NAME','parent'=>$companySpeciesWiseFcr->getFeedType()),['id' => 'ASC']);

        $fcrDetailsMonthWise = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getCompanySpeciesWiseFcrDetailsByReportingMonth($companySpeciesWiseFcr->getFcrOfFeed(), $companySpeciesWiseFcr->getFeedType()->getId(), $reportingDate, $this->getUser()->getId());
        $fcrMonthWise = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->getFishCompanySpeciesWiseFcrByReportingMonth($companySpeciesWiseFcr->getFcrOfFeed(), $companySpeciesWiseFcr->getFeedType(), $reportingDate, $this->getUser());

        return $this->render('@TerminalbdCrm/fishCompanySpeciesWiseFcr/partial/fish-company-species-wise-fcr-details-body.html.twig', [
            'mainCultureSpecies' => $mainCultureSpecies,
            'fcrDetailsMonthWise' => $fcrDetailsMonthWise,
            'fcrMonthWise' => $fcrMonthWise,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/report/{report}/after/new/modal", methods={"GET", "POST"}, name="fish_company_species_wise_fcr_after_modal", options={"expose"=true})
     */
    public function newAfterModal(Request $request, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);

        $form = $this->createForm(CompanySpeciesWiseFcrAfterFormType::class, null, array('report' => $report, 'user' => $this->getUser(),'agentRepo' => $agentRepo));

        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $data = $request->request->get('company_species_wise_fcr_after_form');
//            dd($data);
            $reportingDate = isset($data['reporting_date'])? $data['reporting_date'] : date('Y-m-d',strtotime('now'));
            $date=new \DateTime($reportingDate);
            if(isset($data['feed'])){
                $returnIds=[];
                $feedType = isset($data['feed_type'])?$this->getDoctrine()->getRepository(Setting::class)->find($data['feed_type']):null;
                $agent = isset($data['agent'])?$agentRepo->find($data['agent']):null;
                foreach ($data['feed'] as $feedId){


                    $feed = $this->getDoctrine()->getRepository(Setting::class)->find($feedId);

                    $entity = new FishCompanyAndSpeciesWiseAverageFcr();
                    $existReport = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->findOneBy(array('reportingMonth'=>$date, 'report'=>$report, 'agent'=>$agent, 'employee'=>$this->getUser(),'feed'=>$feed,'feedType'=>$feedType));

                    if($existReport){
                        $entity=$existReport;
                    }

                    if($existReport){
                        $entity->setReportingMonth($entity->getReportingMonth());
                    }else{
                        $entity->setReportingMonth(new \DateTime($reportingDate));
                    }
                    $entity->setAgent($agent?$agent:null);
                    $entity->setReport($report);
                    $entity->setEmployee($this->getUser());
                    $entity->setFeed($feed);
                    $entity->setFcrOfFeed('AFTER');
                    $entity->setFeedType($feedType);
                    $em->persist($entity);

                    $speciesNameByFeedType= $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SPECIES_NAME','status'=>1,'parent'=>$feedType));
                    if($speciesNameByFeedType){
                        foreach ($speciesNameByFeedType as $item){
                            $fishCompanySpeciesWiseFcrDetails = new FishCompanyAndSpeciesWiseAverageFcrDetails();

                            $existDetails = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->findOneBy(array('speciesName'=>$item, 'fishCompanyAndSpeciesWiseAverageFcr'=>$entity));
                            if($existDetails){
                                $fishCompanySpeciesWiseFcrDetails=$existDetails;
                            }
                            $fishCompanySpeciesWiseFcrDetails->setSpeciesName($item);
                            $fishCompanySpeciesWiseFcrDetails->setFishCompanyAndSpeciesWiseAverageFcr($entity);
                            $em->persist($fishCompanySpeciesWiseFcrDetails);
                        }
                    }
                    $returnIds[]=$entity->getId();
                }

                $em->flush();
            }

            $this->addFlash('success', 'post.created_successfully');

            return $this->redirectToRoute('fish_company_species_wise_fcr_details', ['beforeAfter'=>'AFTER','feedType'=>$feedType->getId(),'ids'=>$returnIds,'created_date'=>$reportingDate]);

        }


        return $this->render('@TerminalbdCrm/fishCompanySpeciesWiseFcr/after/fish-company-species-wise-fcr.html.twig', [
            'report' => $report,
            'form' => $form->createView(),
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
