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
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReport;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReportDetails;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\CattleLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\CattleLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Form\FarmerTrainingReportFormType;


/**
 * @Route("/crm/farmer/training")
 */
class FarmerTrainingController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/agent/{id}/purpose/{purpose}/new/modal", methods={"GET", "POST"}, name="farmer_training_report_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, Agent $agent, Setting $purpose): Response
    {

        $entity = new FarmerTrainingReport();

        $data = $request->request->all();

        $requestFarmers = isset($data['farmers'])?$data['farmers']:null;
        $training_materials = isset($data['training_material'])?$data['training_material']:[];
        $trainingDate = isset($data['farmer_training_report_form']['training_date'])?date('Y-m-d',strtotime($data['farmer_training_report_form']['training_date'])):date('Y-m-d',strtotime('now'));

        $form = $this->createForm(FarmerTrainingReportFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $existReport = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->findOneBy(array('employee'=>$this->getUser(), 'agent'=>$agent, 'agentPurpose'=>$purpose, 'trainingDate'=>new \DateTime($trainingDate)));
            if ($existReport){
                $entity= $existReport;
            }

            if($existReport){
                return $this->redirectToRoute('farmer_training_report_detail_modal', ['id'=>$existReport->getId(),'process'=>'postView_'.$existReport->getId(),'action'=>'form_submit']);
            }

            $entity->setTrainingDate(new \DateTime($trainingDate));
            $entity->setAgentPurpose($purpose);
            $entity->setAgent($agent);
            $entity->setTrainingMaterial(json_encode($training_materials));
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
//            return new JsonResponse($requestFarmers);
            foreach ($requestFarmers as $requestFarmer){
                $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($requestFarmer);

                $farmerTrainingDetails = new FarmerTrainingReportDetails();

                $farmerTrainingDetails->setCustomer($farmer);
                $farmerTrainingDetails->setFarmerTrainingReport($entity);
                $farmerTrainingDetails->setFarmerCapacity(json_encode(array()));
                $farmerTrainingDetails->setTrainingMaterialQty(json_encode(array()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($farmerTrainingDetails);

            }
            $em->flush();

            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return new Response('success');
              //  return $this->redirectToRoute('cattle_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
            }
//            return $this->redirectToRoute('cattle_new_modal');
        }
        $farmers = $this->getDoctrine()->getRepository(CrmCustomer::class)->getAgentWise($agent);
        return $this->render('@TerminalbdCrm/farmerTraining/new-modal.html.twig', [
            'agent' => $agent,
            'purpose' => $purpose,
            'farmers' => $farmers,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param FarmerTrainingReport $farmerTrainingReport
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="farmer_training_report_detail_modal")
     */
    public function lifeCycleDetailsModal(FarmerTrainingReport $farmerTrainingReport): Response
    {

        return $this->render('@TerminalbdCrm/farmerTraining/details-modal.html.twig', [
            'farmerTrainingReport' => $farmerTrainingReport,
        ]);
    }

    /**
     * @Route("/materials/breed/{id}/ajax", methods={"POST"}, name="crm_farmer_training_material_ajax", options={"expose"=true})
     */
    public function getFarmerTrainingMaterialByBreedName($id): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$id));

        $arrayData = array();
        /**@var Setting $entity*/
        foreach ($entities as $entity){
                $arrayData[]=array('id'=>$entity->getId(),'text'=>$entity->getName());

        }

        return new JsonResponse($arrayData);
    }

}
