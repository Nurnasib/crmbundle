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
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarmMedicineOrVaccineCost;
use Terminalbd\CrmBundle\Entity\BroilerLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/antibiotic/free/farm")
 */
class AntibioticFreeFarmController extends AbstractController
{
    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="antibiotic_free_farm_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $reportParentParent= $report->getParent()->getParent();
        $entity = new AntibioticFreeFarm();

        $allRequest = $request->request->all();


        $form = $this->createForm(AntibioticFreeFarmFormType::class, $entity,array('report' => $report))
            ->add('SaveAndCreate', ButtonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            dd($allRequest);
            $data = $allRequest['antibiotic_free_farm_form'];

            $medicine_name = $allRequest['medicine_name'];
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
            $vaccine_price = $allRequest['vaccine_price'];

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
//            $em->flush();

            foreach ($medicine_name as $key=>$value){
                if($value!=''&&$medicine_duration_age[$key]!=''){

                    $medicine = new AntibioticFreeFarmMedicineOrVaccineCost();

                    $medicine->setMedicineOrVaccineName($value);

                    $medicine->setAgeDays((float)$medicine_duration_age[$key]);

                    $medicine->setPurposeOrDisease($medicine_purpose[$key]);
                    $medicine->setDosage($medicine_dosage[$key]);
                    $medicine->setQuantity((float)$medicine_quantity[$key]);
                    $medicine->setPrice((float)$medicine_price[$key]);
                    $medicine->setCostType(AntibioticFreeFarmMedicineOrVaccineCost::COST_TYPE_MEDICINE);

                    $medicine->setCrmAntibioticFreeFarm($entity);

                    $em->persist($medicine);
                    $em->flush();

                }


            }

            foreach ($vaccine_name as $keyVaccine=>$valueVaccine){
                if($value!=''&&$vaccine_duration_age[$keyVaccine]!=''){
                    $vaccine = new AntibioticFreeFarmMedicineOrVaccineCost();
                    $vaccine->setMedicineOrVaccineName($valueVaccine);
                    $vaccine->setAgeDays((float)$vaccine_duration_age[$keyVaccine]);
                    $vaccine->setPurposeOrDisease($vaccine_purpose[$keyVaccine]);
                    $vaccine->setDosage($vaccine_dosage[$keyVaccine]);
                    $vaccine->setQuantity((float)$vaccine_quantity[$keyVaccine]);
                    $vaccine->setPrice((float)$vaccine_price[$keyVaccine]);
                    $vaccine->setCrmAntibioticFreeFarm($entity);
                    $vaccine->setCostType(AntibioticFreeFarmMedicineOrVaccineCost::COST_TYPE_VACCINE);
                    $em->persist($vaccine);

                }
            }

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
     * @param AntibioticFreeFarm $antibioticFreeFarm
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
