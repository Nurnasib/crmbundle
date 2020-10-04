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
//use Terminalbd\CrmBundle\Entity\BroilerStandard;
//use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Form\FcrFormType;


/**
 * @Route("/crm/fcr")
 */
class FcrController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="fcr")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(Fcr::class)->findBy(
            ['fcrOfFeed'=>'BEFORE']
        );
        return $this->render('@TerminalbdCrm/fcr/index.html.twig',['entities' => $entitys]);
    }


    /**
     * @Route("/after", methods={"GET"}, name="fcr_after")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index_after(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(Fcr::class)->findBy(
            ['fcrOfFeed'=>'AFTER']
        );
        return $this->render('@TerminalbdCrm/fcr/after_index.html.twig',['entities' => $entitys]);
    }




    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     * @Route("/new", methods={"GET", "POST"}, name="fcr_new")
     */
    public function new(Request $request): Response
    {
        $entity = new Fcr();
       $data = $request->request->all();

        $form = $this->createForm(FcrFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('fcr_new');
            }
            return $this->redirectToRoute('fcr');
        }
        return $this->render('@TerminalbdCrm/fcr/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="fcr_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */

    public function edit(Request $request, Fcr $entity): Response
    {
        $data = $request->request->all();
        $form = $this->createForm(FcrFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('fcr', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('fcr');
        }
        return $this->render('@TerminalbdCrm/fcr/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Fcr entity.
     * @Route("/{id}/delete", methods={"GET"}, name="fcr_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(Fcr::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    



}
