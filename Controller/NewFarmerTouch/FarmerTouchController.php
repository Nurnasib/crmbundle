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
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
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
        $customerType= $crmCustomer->getFarmerTouch()?$crmCustomer->getFarmerTouch()->getFarmerType()->getSlug():null;
        if(!$customerType){
            return $this->redirectToRoute('crm_visit');
        }

        $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$crmCustomer->getFarmerTouch()->getFarmerType()->getId()));

        $entity = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->findOneBy(array('customer'=>$crmCustomer));

        if($customerType=='poultry-breed'){
            $form = $this->createForm(PoultryFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }elseif ($customerType=='cattle-breed'){
            $form = $this->createForm(CattleFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }else{
            $form = $this->createForm(FishFarmerTouchFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entity->setVisitingDate(new \DateTime());

            $entity->setCultureSpeciesItemAndQty(json_encode($data['fish_specie']));
            $entity->setReport($report);

            $farmerIntroduce = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->findOneBy(array('customer'=>$crmCustomer));

            $farmerIntroduce->setCultureSpeciesItemAndQty($entity->getCultureSpeciesItemAndQty());
            $farmerIntroduce->setRemarks($entity->getRemarks());
            $farmerIntroduce->setCreatedAt(new \DateTime());


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($farmerIntroduce);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }

        if($customerType=='poultry-breed'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/poultry-new-modal.html.twig', [
                'report' => $report,
                'employee' => $this->getUser(),
                'crmCustomer' => $crmCustomer,
                'fishSpecies' => $species,
                'form' => $form->createView(),
                'entity'=>$entity
            ]);
        }elseif ($customerType=='cattle-breed'){
            return $this->render('@TerminalbdCrm/farmerTouchReport/cattle-new-modal.html.twig', [
                'report' => $report,
                'employee' => $this->getUser(),
                'crmCustomer' => $crmCustomer,
                'fishSpecies' => $species,
                'form' => $form->createView(),
                'entity'=>$entity
            ]);
        }

        return $this->render('@TerminalbdCrm/farmerTouchReport/fish-new-modal.html.twig', [
            'report' => $report,
            'employee' => $this->getUser(),
            'crmCustomer' => $crmCustomer,
            'fishSpecies' => $species,
            'form' => $form->createView(),
            'entity'=>$entity
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
