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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CostBenefitAnalysisLessCostingFarmForFishFormType;
use Terminalbd\CrmBundle\Form\CostBenefitAnalysisLessCostingFarmForPoultryFormType;


/**
 * @Route("/crm/cost/benefit/analysis/less/costing/farm")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_DEVELOPER')")
 */
class CostBenefitAnalysisForLessCostingFarmController extends AbstractController
{
    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @param Setting $parentParent
     * @return Response
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @ParamConverter("report", class="Terminalbd\CrmBundle\Entity\Setting")
     * @ParamConverter("parentParent", class="Terminalbd\CrmBundle\Entity\Setting")
     * @Route("/customer/{id}/report/{report}/parentParent/{parentParent}/new/modal", methods={"GET", "POST"}, name="cost_benefit_analysis_less_costing_farm_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report, Setting $parentParent): Response
    {
        $em = $this->getDoctrine()->getManager();

//        $parentParent= $report->getParent()->getParent();
        $entity = new CostBenefitAnalysisForLessCostingFarm();

        $allRequest = $request->request->all();

        if($parentParent->getSlug()=='poultry-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            $farmTypesByParentPoultry = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FARM_TYPE','parent'=>$parentParent));

            $farmTypeId = [];
            foreach ($farmTypesByParentPoultry as $value){
                $farmTypeId[]= $value->getId();
            }
            $form = $this->createForm(CostBenefitAnalysisLessCostingFarmForPoultryFormType::class, $entity,array('report' => $report, 'farmTypeId'=>$farmTypeId))
                ->add('SaveAndCreate', ButtonType::class);
        }elseif ($parentParent->getSlug()=='fish-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            $form = $this->createForm(CostBenefitAnalysisLessCostingFarmForFishFormType::class, $entity,array('report' => $report))
                ->add('SaveAndCreate', ButtonType::class);
        }else{
            $form = $this->createForm(CostBenefitAnalysisLessCostingFarmForPoultryFormType::class, $entity,array('report' => $report))
                ->add('SaveAndCreate', ButtonType::class);
        }


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($parentParent->getSlug()=='poultry-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
                $data = $allRequest['cost_benefit_analysis_less_costing_farm_for_poultry_form'];
            }elseif ($parentParent->getSlug()=='fish-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
                $data = $allRequest['cost_benefit_analysis_less_costing_farm_for_fish_form'];
            }else{
                $data = $allRequest['cost_benefit_analysis_less_costing_farm_for_poultry_form'];
            }

            $reporting_month= '01-'.$data['reporting_month'];
//            return new JsonResponse($medicine_name[0]);
            $existReport = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->getCostBenefitAnalysisByReportingMonthEmployeeCustomerAndReport($report, $this->getUser(), $crmCustomer, $reporting_month);
            if ($existReport){
                return new JsonResponse(array(
                    'id'=> $existReport->getId(),
                    'status'=> 'old'
                ));
            }

            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setReportParentParent($parentParent);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setEmployee($this->getUser());


            $entity->setFcr($entity->calculateFcr());


            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array(
                'id'=> $entity->getId(),
                'status'=> 'new'
            ));
        }

        if($parentParent->getSlug()=='poultry-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/poultry-new-modal.html.twig', [
                'report' => $report,
                'crmCustomer' => $crmCustomer,
                'entity' => $entity,
                'form' => $form->createView(),
            ]);
        }elseif ($parentParent->getSlug()=='fish-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/fish-new-modal.html.twig', [
                'report' => $report,
                'crmCustomer' => $crmCustomer,
                'entity' => $entity,
                'form' => $form->createView(),
            ]);
        }else{
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/poultry-new-modal.html.twig', [
                'report' => $report,
                'crmCustomer' => $crmCustomer,
                'entity' => $entity,
                'form' => $form->createView(),
            ]);
        }


    }

    /**
     * @param $id
     * @return Response
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="cost_benefit_analysis_less_costing_farm_detail_modal", options={"expose"=true})
     */
    public function lessCostingFarmDetailsModal($id): Response
    {
        $lessCostingFarm = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->find($id);
        $parentParent= $lessCostingFarm->getReportParentParent();


        if($parentParent->getSlug()=='poultry-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/poultry-details-modal.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
            ]);
        }elseif ($parentParent->getSlug()=='fish-breed' && $parentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/fish-details-modal.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
            ]);
        }else{
            return $this->render('@TerminalbdCrm/costBenefitAnalysisLessCostingFarm/poultry-details-modal.html.twig', [
                'lessCostingFarm' => $lessCostingFarm,
            ]);
        }

    }


}
