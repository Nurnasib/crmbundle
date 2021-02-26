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

use App\Entity\Admin\Location;
use App\Entity\Core\ItemKeyValue;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Terminalbd\CrmBundle\Form\EditEmployeeFormType;
use Terminalbd\CrmBundle\Form\EmployeeFormType;


/**
 * @Route("/crm/employee")
 */
class EmployeeController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_employee")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(User::class)->findBy(array('userGroup'=>'9'));
        return $this->render('@TerminalbdCrm/employee/index.html.twig',['entities' => $entities]);
    }


    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/register", methods={"GET", "POST"}, name="crm_employee_register")
     */
    public function register(Request $request): Response
    {
//        $passwordEncoder = UserPasswordEncoderInterface::class;
        $user = new User();
        $data = $request->request->all();
        $terminal = $this->getUser()->getTerminal();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $form = $this->createForm(EmployeeFormType::class, $user, array('terminal' => $terminal,'userRepo'=>$userRepo , 'locationRepo' => $locationRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        $errors = $this->getErrorsFromForm($form);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('crm_bundle.user_manager')->setUserPassword($user, $form->get('password')->getData());
            $user->setTerminal($terminal);
            $em->persist($user);
            $em->flush();
            $this->getDoctrine()->getRepository(ItemKeyValue::class)->insertUserKeyValue($user,$data);
            return $this->redirectToRoute('crm_employee');
        }
        return $this->render('@TerminalbdCrm/employee/register.html.twig', [
            'id' => 'postForm',
            'post' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_employee_edit")
     */
    public function edit(Request $request ,$id): Response
    {
        $data = $request->request->all();
        $post = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id'=> $id]);
        $terminal = $this->getUser()->getTerminal();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(EditEmployeeFormType::class, $post, array('terminal' => $terminal,'userRepo' => $userRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        //  $errors = $this->getErrorsFromForm($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('crm_employee_edit',array('id'=> $post->getId()));
        }
        return $this->render('@TerminalbdCrm/employee/editRegister.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }



    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }



}
