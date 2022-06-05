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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\SettingFormType;
use Terminalbd\CrmBundle\Form\TrainingMaterialFormType;


/**
 * @Route("/crm/training/material")
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class TrainingMaterialController extends AbstractController
{

    /**
     * @Route("/list", methods={"GET", "POST"}, name="training_material_list", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request){
        if (in_array('ROLE_DEVELOPER',$this->getUser()->getRoles())){
            $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL'),array('settingType'=>'asc'));
        }elseif (in_array('ROLE_CRM_CATTLE_ADMIN',$this->getUser()->getRoles()) && in_array('ROLE_CRM_POULTRY_ADMIN',$this->getUser()->getRoles())){
            $breedName= $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'BREED_NAME','slug'=>['cattle-breed','poultry-breed'],'status'=>1]);
            $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedName));
        }elseif (in_array('ROLE_CRM_AQUA_ADMIN',$this->getUser()->getRoles())){
            $breedName= $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'BREED_NAME','slug'=>['fish-breed'],'status'=>1]);
            $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedName));
        }elseif (in_array('ROLE_CRM_POULTRY_ADMIN',$this->getUser()->getRoles()) && !in_array('ROLE_CRM_CATTLE_ADMIN',$this->getUser()->getRoles())){
            $breedName= $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'BREED_NAME','slug'=>['poultry-breed'],'status'=>1]);
            $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedName));
        }elseif (in_array('ROLE_CRM_CATTLE_ADMIN',$this->getUser()->getRoles()) && !in_array('ROLE_CRM_POULTRY_ADMIN',$this->getUser()->getRoles())){
            $breedName= $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType'=>'BREED_NAME','slug'=>['cattle-breed'],'status'=>1]);
            $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedName));
        }
        return $this->render('@TerminalbdCrm/trainingMaterial/index.html.twig',['entities' => $entities]);
    }


    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_training_material_create")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $entity = new Setting();

        $form = $this->createForm(TrainingMaterialFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_training_material_create');
            }
            return $this->redirectToRoute('training_material_list');
        }
        return $this->render('@TerminalbdCrm/trainingMaterial/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_training_material_edit")
     * @param Request $request
     * @param Setting $entity
     * @return Response
     */

    public function edit(Request $request, Setting $entity): Response
    {
        $form = $this->createForm(TrainingMaterialFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            //$this->getDoctrine()->getRepository(ItemKeyValue::class)->insertSettingKeyValue($entity,$data);
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('training_material_list');
            }
            return $this->redirectToRoute('training_material_list');
        }
        return $this->render('@TerminalbdCrm/trainingMaterial/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }
}
