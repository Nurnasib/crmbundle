<?php

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
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarmMedicineOrVaccineCost;
use Terminalbd\CrmBundle\Entity\BroilerLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/antibiotic/free/farm")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER)")
 */
class AntibioticFreeFarmController extends AbstractController
{
    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @return Response
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="antibiotic_free_farm_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $reportParentParent= $report->getParent()->getParent();
        $entity = new AntibioticFreeFarm();

        $allRequest = $request->request->all();

        $farmTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FARM_TYPE','parent'=>$reportParentParent));

        $farmTypeId = [];
        if($farmTypesByParent){
            foreach ($farmTypesByParent as $value){
                $farmTypeId[]= $value->getId();
            }
        }

        $form = $this->createForm(AntibioticFreeFarmFormType::class, $entity,array('report' => $report, 'farmTypeId'=>$farmTypeId))
            ->add('SaveAndCreate', ButtonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            dd($allRequest);
            $data = $allRequest['antibiotic_free_farm_form'];

            /*$medicine_name = $allRequest['medicine_name'];
            $medicine_duration_age = $allRequest['medicine_duration_age'];
            $medicine_purpose = $allRequest['medicine_purpose'];
            $medicine_dosage = $allRequest['medicine_dosage'];
            $medicine_quantity = $allRequest['medicine_quantity'];
            $medicine_price = $allRequest['medicine_price'];

            $vaccine_name = $allRequest['vaccine_name'];
            $vaccine_duration_age = $allRequest['vaccine_duration_age'];
            $vaccine_purpose = $allRequest['vaccine_purpose'];
            $vaccine_dosage = $allRequest['vaccine_dosage'];
            $vaccine_quantity = $allRequest['vaccine_quantity'];
            $vaccine_price = $allRequest['vaccine_price'];*/

            $reporting_month= '01-'.$data['reporting_month'];
//            return new JsonResponse($medicine_name[0]);
            $existReport = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmByReportingMonthEmployeeCustomerAndReport($report, $this->getUser(), $crmCustomer, $reporting_month);
            if ($existReport){
                return new JsonResponse(array(
                    'id'=> $existReport->getId(),
                    'status'=> 'old'
                ));
            }

            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setReportParentParent($reportParentParent);
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
        return $this->render('@TerminalbdCrm/antibioticFreeFarm/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param $id
     * @return Response
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="antibiotic_free_farm_detail_modal", options={"expose"=true})
     */
    public function antibioticFreeFarmDetailsModal($id): Response
    {

        $antibioticFreeFarm = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->find($id);
        return $this->render('@TerminalbdCrm/antibioticFreeFarm/details-modal.html.twig', [
            'antibioticFreeFarm' => $antibioticFreeFarm,
        ]);
    }


}
