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
use Terminalbd\CrmBundle\Form\FarmerIntroduceFormType;
use Terminalbd\CrmBundle\Form\NewFarmerTouch\FishFarmerTouchFormType;
use Terminalbd\CrmBundle\Form\NewFarmerTouch\PoultryFarmerTouchFormType;


/**
 * @Route("/crm/farmer/introduce")
 */
class FarmerIntroduceController extends AbstractController
{

    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="farmer_introduce_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $data = $request->request->all();
        $customerType= $crmCustomer->getFarmerIntroduce()?$crmCustomer->getFarmerIntroduce()->getFarmerType()->getSlug():null;
        if(!$customerType){
            return $this->redirectToRoute('crm_visit');
        }

        $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$crmCustomer->getFarmerIntroduce()->getFarmerType()->getId()));

        $entity = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->findOneBy(array('customer'=>$crmCustomer));
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);

        $form = $this->createForm(FarmerIntroduceFormType::class, $entity,array('user' => $this->getUser(),'report' =>$report,'agentRepo' => $agentRepo));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entity->setCultureSpeciesItemAndQty(json_encode($data['fish_specie']));
            $entity->setCreatedAt(new \DateTime());
            $farmerTouch = $this->getDoctrine()->getRepository(FarmerTouchReport::class)->findOneBy(array('customer'=>$crmCustomer));
            $farmerTouch->setRemarks($entity->getRemarks());
            $farmerTouch->setAgent($entity->getAgent());
            $farmerTouch->setFeed($entity->getFeed());

            $crmCustomer->setAgent($entity->getAgent());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($farmerTouch);
            $em->persist($crmCustomer);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            return new Response('success');
        }

        return $this->render('@TerminalbdCrm/farmerIntroduce/introduce-new-modal.html.twig', [
            'report' => $report,
            'employee' => $this->getUser(),
            'crmCustomer' => $crmCustomer,
            'fishSpecies' => $species,
            'form' => $form->createView(),
            'entity'=>$entity
        ]);
    }


}
