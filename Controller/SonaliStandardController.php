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
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Form\SonaliStandardFormType;

/**
 * @Route("/crm/sonali/standard")
 */
class SonaliStandardController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="sonali_standard")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(SonaliStandard::class)->findAll();
        return $this->render('@TerminalbdCrm/sonaliStandard/index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="sonali_standard_new")
     */
    public function new(Request $request): Response
    {

        $entity = new SonaliStandard();
        $data = $request->request->all();
        $form = $this->createForm(SonaliStandardFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('sonali_standard_new');
            }
            return $this->redirectToRoute('sonali_standard_new');
        }
        return $this->render('@TerminalbdCrm/sonaliStandard/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing SonaliStandard entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="sonali_edit")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, SonaliStandard $entity): Response
    {
        $data = $request->request->all();
        $form = $this->createForm(SonaliStandardFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('sonali_standard');
            }
            return $this->redirectToRoute('sonali_standard');
        }
        return $this->render('@TerminalbdCrm/sonaliStandard/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a SonaliStandard entity.
     * @Route("/{id}/delete", methods={"GET"}, name="sonali_delete")
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(SonaliStandard::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    /**
     * @Route("/weight/by/age", methods={"POST"}, name="crm_sonali_weight_standard_by_age", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function getSonaliWeightStandardByAge(Request $request): Response
    {
        $data = $request->request->all();
        /**@var SonaliStandard $entity*/
        $entity = $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$data['ageDays']));
        return new JsonResponse(
            array(
                'success'=>'Success',
                'weightStandard'=>$entity->getTargetBodyWeight(),
                'status'=>200,
            )
        );

    }
    



}
