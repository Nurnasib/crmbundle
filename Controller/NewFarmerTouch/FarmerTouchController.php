<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Controller\NewFarmerTouch;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\NewFarmerTouch\CattleFarmerTouchFormType;
use Terminalbd\CrmBundle\Form\NewFarmerTouch\FishFarmerTouchFormType;
use Terminalbd\CrmBundle\Form\NewFarmerTouch\PoultryFarmerTouchFormType;


/**
 * @Route("/crm/farmer/touch")
 */
class FarmerTouchController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="farmer_touch_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $data = $request->request->all();
        $reportParentParent= $report->getParent()->getParent();
        $fishSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$reportParentParent));

        $entity = new FarmerTouchReport();

        if($reportParentParent->getSlug()=='poultry-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            $form = $this->createForm(PoultryFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }elseif ($reportParentParent->getSlug()=='cattle-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            $form = $this->createForm(CattleFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }else{
            $form = $this->createForm(FishFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $reporting_date = date('Y-m-d',strtotime('now'));

            $entity->setVisitingDate(new \DateTime($reporting_date));

            $entity->setReportParentParent($reportParentParent);

            $entity->setCultureSpeciesItemAndQty(json_encode($data['fish_specie']));
            $entity->setCustomer($crmCustomer);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setReport($report);
            $entity->setEmployee($this->getUser());


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }
        $farmerTouchReports = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFishFarmerTouchReportByDateAndEmployeeAndReport($reportParentParent,$this->getUser());

        if($reportParentParent->getSlug()=='poultry-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/poultry-new-modal.html.twig', [
                'report' => $report,
                'employee' => $this->getUser(),
                'crmCustomer' => $crmCustomer,
                'fishSpecies' => $fishSpecies,
                'form' => $form->createView(),
                'farmerTouchReports' => $farmerTouchReports,
                'reportParentParent' => $reportParentParent,
            ]);
        }elseif ($reportParentParent->getSlug()=='cattle-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/cattle-new-modal.html.twig', [
                'report' => $report,
                'employee' => $this->getUser(),
                'crmCustomer' => $crmCustomer,
                'fishSpecies' => $fishSpecies,
                'form' => $form->createView(),
                'farmerTouchReports' => $farmerTouchReports,
                'reportParentParent' => $reportParentParent,
            ]);
        }

        return $this->render('@TerminalbdCrm/farmerTouchReport/fish-new-modal.html.twig', [
            'report' => $report,
            'employee' => $this->getUser(),
            'crmCustomer' => $crmCustomer,
            'fishSpecies' => $fishSpecies,
            'form' => $form->createView(),
            'farmerTouchReports' => $farmerTouchReports,
            'reportParentParent' => $reportParentParent,
        ]);
    }

    /**
     * @Route("/{id}/refresh", methods={"GET"}, name="crm_farmer_touch_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function farmerTouchReportRefresh(Setting $report): Response
    {
        $reportParentParent= $report->getParent()->getParent();
        $fishSpecies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$reportParentParent));

        $farmerTouchReport = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->getFishFarmerTouchReportByDateAndEmployeeAndReport($reportParentParent,$this->getUser());

        if($reportParentParent->getSlug()=='poultry-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/partial/poultry-farmer-touch-report-body.html.twig', [
                'farmerTouchReports' => $farmerTouchReport,
                'fishSpecies' => $fishSpecies,
            ]);
        }elseif ($reportParentParent->getSlug()=='cattle-breed' && $reportParentParent->getSettingType()== 'BREED_NAME'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/partial/cattle-farmer-touch-report-body.html.twig', [
                'farmerTouchReports' => $farmerTouchReport,
                'fishSpecies' => $fishSpecies,
            ]);
        }

        return $this->render('@TerminalbdCrm/farmerTouchReport/partial/fish-farmer-touch-report-body.html.twig', [
            'farmerTouchReports' => $farmerTouchReport,
            'fishSpecies' => $fishSpecies,
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/{id}/delete", methods={"POST"}, name="fish_farmer_touch_report_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

}
