<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;


use App\Entity\Core\Agent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Terminalbd\CrmBundle\Form\CrmVisitFormType;
use Terminalbd\CrmBundle\Repository\CrmVisitRepository;

class CrmVisitController extends AbstractController
{

    /**
     * @Route("/crm/visit", methods={"GET"}, name="crm_visit")
     */
    public function index()
    {
        $entities= $this->getDoctrine()->getRepository(CrmVisit::class)->findAll();
        return $this->render('@TerminalbdCrm/crmvisit/index.html.twig',[
            'entities' => $entities
        ]);
    }

    /**
     * @param Request $request
     * @Route("/crm/visit/new" ,name="new_visit")
     */
    public function create(Request $request){

        $entity = new CrmVisit();
        $form = $this->createForm(CrmVisitFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

           $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertCrmVisiDetailstKeyValue($entity,$data);

            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('new_visit');
            }
            return $this->redirectToRoute('crm_visit');
        }
        $customerGroups= $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'CUSTOMER_GROUP'));
        $agent=$this->getDoctrine()->getRepository(Agent::class)->findAll(array('agents'=>'agents'));

        return $this->render('@TerminalbdCrm/crmvisit/create.html.twig', [
            'entity' => $entity,
            'customerGroups' => $customerGroups,
            'agents'=>$agent,
            'form' => $form->createView(),
        ]);



    }








}