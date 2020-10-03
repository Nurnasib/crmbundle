<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

/**
 * @Route("/crm/customer")
 */

class CrmCustomerController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="crm_customer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index()
    {
        $entities=$this->getDoctrine()->getRepository(CrmCustomer::class)->findAll();

        return $this->render('@TerminalbdCrm/crmcustomer/index.html.twig', [
            'entities' =>$entities
        ]);
    }


    /**
     * @param Request $request
     * @Route("/store/ajax" ,name="new_customer_ajax", methods={"POST"}, options={"expose"=true})
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


    /**
     * Deletes a CrmCustomer entity.
     * @Route("/{id}/delete", methods={"GET"}, name="customer_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }






}