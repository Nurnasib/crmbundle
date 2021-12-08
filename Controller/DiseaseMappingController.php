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
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\DiseaseMappingFormType;


/**
 * @Route("/crm/disease/mapping")
 */
class DiseaseMappingController extends AbstractController
{
    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="disease_mapping_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $diseasesMapping = $this->getDoctrine()->getRepository(DiseaseMapping::class)->getDiseaseMappingByCreatedDateEmployeeReport($report, $this->getUser());

        $entity = new DiseaseMapping();

        $form = $this->createForm(DiseaseMappingFormType::class, $entity,array('report' => $report));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setEmployee($this->getUser());
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array(
                'id'=> $entity->getId(),
                'status'=> 'new'
            ));
        }
        return $this->render('@TerminalbdCrm/diseaseMapping/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'diseasesMapping' => $diseasesMapping,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param AntibioticFreeFarm $antibioticFreeFarm
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="disease_mapping_detail_modal", options={"expose"=true})
     */
    public function diseaseMappingDetailsModal(DiseaseMapping $diseaseMapping): Response
    {
        $diseasesMapping = $this->getDoctrine()->getRepository(DiseaseMapping::class)->getDiseaseMappingByCreatedDateEmployeeReport($diseaseMapping->getReport(), $this->getUser());
        return $this->render('@TerminalbdCrm/diseaseMapping/details-modal.html.twig', [
            'diseasesMapping' => $diseasesMapping,
        ]);
    }

    /**
     * Deletes a Disease Mapping entity.
     * @Route("/{id}/delete", methods={"POST"}, name="disease_mapping_delete")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(DiseaseMapping::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


}
