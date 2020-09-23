<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

class CrmCustomerController extends AbstractController
{

    /**
     * @Route("/crm/customer", methods={"GET"}, name="crm_customer")
     */
    public function index()
    {
        $entities=$this->getDoctrine()->getRepository(CrmCustomer::class)->findAll();

        return $this->render('@TerminalbdCrm/crmcustomer/index.html.twig', [
            'entities' =>$entities
        ]);
    }

//
//    /**
//     * @param Request $request
//     * @Route("/crm/customer/new" ,name="new_customer", options={"expose"=true})
//     */
//    public function create(Request $request){
//
//        $entity = new CrmCustomer();
//        $form=$this->createForm(CrmCustomerFormType::class,$entity)
//            ->add('SaveAndCreate', SubmitType::class);
//        $form->handleRequest($request);
//        if($form->isSubmitted() && $form->isValid() ){
//            $em=$this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//            $this->addFlash('success', 'post.created_successfully');
//            if ($form->get('SaveAndCreate')->isClicked()) {
//                return $this->redirectToRoute('new_customer');
//            }
//        }
//        return $this->render('@TerminalbdCrm/crmvisit/create.html.twig',[
//            'form'=>$form->createView(),
//            'entity' => $entity,
//        ]);
//
//    }

    /**
     * @param Request $request
     * @Route("/crm/customer/store/ajax" ,name="new_customer_ajax", methods={"POST"}, options={"expose"=true})
     */
    public function store(Request $request){

        $entity=new CrmCustomer();
        $allRequestData = $request->request->all();
        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setCustomerGroup($allRequestData['custom_group']);
        $entity->setAgentId($allRequestData['agentId']);
        $entity->setSubAgentId($allRequestData['subagentId']);
        $entity->setLocation($allRequestData['location']);
        $em=$this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();


        $returnData= array(
          'id'=>$entity->getId(),
          'name'=>$entity->getName(),
        );

//        return $this->render('@TerminalbdCrm/crmvisit/create.html.twig',[
//            'id'=>$entity->getId(),
//            'name'=>$entity->getName(),
//            'setting'=>$setting
//        ]);

        return new JsonResponse(array($returnData));

    }





}