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
use Terminalbd\CrmBundle\Entity\ComplainDifferentProduct;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ComplainDifferentProductFormType;
use Terminalbd\CrmBundle\Form\DiseaseMappingFormType;


/**
 * @Route("/crm/complain/differnt/product")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_DEVELOPER)")
 */
class ComplainDifferentProductController extends AbstractController
{
    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @return Response
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="complain_different_product_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $complains = $this->getDoctrine()->getRepository(ComplainDifferentProduct::class)->getComplainByCreatedDateEmployeeReport($report, $this->getUser());

        $entity = new ComplainDifferentProduct();

        $farmTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FARM_TYPE','parent'=>$report->getParent()->getParent()));

        $farmTypeId = [];
        if($farmTypesByParent){
            foreach ($farmTypesByParent as $value){
                $farmTypeId[]= $value->getId();
            }
        }

        $productsName = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeByParentParentChildren($farmTypeId);

        $form = $this->createForm(ComplainDifferentProductFormType::class, $entity,array('report' => $report, 'farmTypeId'=>$farmTypeId));
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
        return $this->render('@TerminalbdCrm/complainDifferentProduct/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'complains' => $complains,
            'productsName' => $productsName,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param ComplainDifferentProduct $complainDifferentProduct
     * @return Response
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="complain_different_product_detail_modal", options={"expose"=true})
     */
    public function detailsModal(ComplainDifferentProduct $complainDifferentProduct): Response
    {
        $farmTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FARM_TYPE','parent'=>$complainDifferentProduct->getReport()->getParent()->getParent()));

        $farmTypeId = [];
        if($farmTypesByParent){
            foreach ($farmTypesByParent as $value){
                $farmTypeId[]= $value->getId();
            }
        }

        $productsName = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeByParentParentChildren($farmTypeId);

        $complains = $this->getDoctrine()->getRepository(ComplainDifferentProduct::class)->getComplainByCreatedDateEmployeeReport($complainDifferentProduct->getReport(), $this->getUser());
        return $this->render('@TerminalbdCrm/complainDifferentProduct/details-modal.html.twig', [
            'complains' => $complains,
            'productsName' => $productsName,
        ]);
    }

    /**
     * Deletes a Disease Mapping entity.
     * @Route("/{id}/delete", methods={"POST"}, name="complain_different_product_delete")
     * @param $id
     * @return Response
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(ComplainDifferentProduct::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


}
