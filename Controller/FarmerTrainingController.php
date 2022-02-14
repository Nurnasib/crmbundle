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
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_DEVELOPER)")
 */
class FarmerTrainingController extends AbstractController
{

    /**
     * @Route("/agent/{id}/purpose/{purpose}/new/modal", methods={"GET", "POST"}, name="farmer_training_report_new_modal", options={"expose"=true})
     * @param Request $request
     * @param Agent $agent
     * @param Setting $purpose
     * @return Response
     * @throws \Exception
     */
    public function newModal(Request $request, Agent $agent, Setting $purpose): Response
    {

        $entity = new FarmerTrainingReport();

        $data = $request->request->all();
        $breedName=null;
        $serviceMode = $this->getUser()->getServiceMode() && $this->getUser()->getServiceMode()->getSlug()!=''?$this->getUser()->getServiceMode()->getSlug():'null';
        if($serviceMode && $serviceMode!='sales-marketing' ){
            $serviceModeExplode=explode('-', $serviceMode);
            $lastElement = end($serviceModeExplode);
            $breedName= $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['settingType'=>'BREED_NAME','slug'=>$lastElement.'-breed','status'=>1]);
        }
        

        $requestFarmers = isset($data['farmers'])?$data['farmers']:null;
        $training_materials = isset($data['training_material'])?$data['training_material']:[];
        $training_material_qty = isset($data['training_material_qty'])?$data['training_material_qty']:[];
        $selectedMaterialQty = array_intersect_key($training_material_qty, $training_materials);

        $species_capacity = isset($data['species_capacity'])?$data['species_capacity']:[];

        $trainingDate = isset($data['farmer_training_report_form']['training_date'])?date('Y-m-d',strtotime($data['farmer_training_report_form']['training_date'])):date('Y-m-d',strtotime('now'));

        $form = $this->createForm(FarmerTrainingReportFormType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(sizeof($requestFarmers)<=0){
                return false;
            }

            $existReport = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->findOneBy(array('employee'=>$this->getUser(), 'agent'=>$agent, 'agentPurpose'=>$purpose, 'trainingDate'=>new \DateTime($trainingDate)));
            if ($existReport){
                $entity= $existReport;
            }

            $entity->setTrainingDate(new \DateTime($trainingDate));
            $entity->setAgentPurpose($purpose);
            $entity->setAgent($agent);
            $entity->setTrainingMaterial(json_encode($training_materials));
            if($breedName){
                $entity->setBreedName($breedName);
            }
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
//            return new JsonResponse($requestFarmers);
            foreach ($requestFarmers as $requestFarmer){
                $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($requestFarmer);

                $existingFarmerTrainingDetails = $this->getDoctrine()->getRepository(FarmerTrainingReportDetails::class)->findOneBy(['farmerTrainingReport'=>$entity,'customer'=>$farmer]);

                $farmerTrainingDetails = new FarmerTrainingReportDetails();

                if($existingFarmerTrainingDetails){
                    $farmerTrainingDetails=$existingFarmerTrainingDetails;
                }

                $farmerTrainingDetails->setCustomer($farmer);
                $farmerTrainingDetails->setFarmerTrainingReport($entity);
                $farmerTrainingDetails->setFarmerCapacity(json_encode($species_capacity[$requestFarmer]));
                $farmerTrainingDetails->setTrainingMaterialQty(json_encode($selectedMaterialQty));
                $em = $this->getDoctrine()->getManager();
                $em->persist($farmerTrainingDetails);

            }
            $em->flush();
            if($existReport){
                return new JsonResponse(array(
                        'status'=>'old',
                        'id'=>$entity->getId()
                    )
                );
            }
            return new JsonResponse(array(
                    'status'=>'new',
                    'id'=>$entity->getId()
                )
            );
              //  return $this->redirectToRoute('cattle_new_modal', ['id'=>$crmCustomer->getId(),'report'=>$report->getId()]);
//            return $this->redirectToRoute('cattle_new_modal');
        }
        $farmers = $this->getDoctrine()->getRepository(CrmCustomer::class)->getAgentWise($agent, $this->getUser());
        $trainingMaterials = $this->getTrainingMaterialAndSpeciesByBreedName();
        return $this->render('@TerminalbdCrm/farmerTraining/new-modal.html.twig', [
            'agent' => $agent,
            'purpose' => $purpose,
            'farmers' => $farmers,
            'entity' => $entity,
            'trainingMaterials' => $trainingMaterials,
            'breedName' => $breedName,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param $id
     * @return Response
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="farmer_training_report_detail_modal", options={"expose"=true})
     */
    public function farmerTrainingDetailsModal($id): Response
    {

        $farmerTrainingReport = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->find($id);
        $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$farmerTrainingReport->getBreedName()->getId()));
        return $this->render('@TerminalbdCrm/farmerTraining/details-modal.html.twig', [
            'farmerTrainingReport' => $farmerTrainingReport,
            'species' => $species,
        ]);
    }

    private function getTrainingMaterialAndSpeciesByBreedName(){

        $arrayData = array();
        $serviceMode = $this->getUser()->getServiceMode() && $this->getUser()->getServiceMode()->getSlug()!=''?$this->getUser()->getServiceMode()->getSlug():'null';
       if($serviceMode && $serviceMode!='sales-marketing' ){
           $serviceModeExplode=explode('-', $serviceMode);
           $lastElement = end($serviceModeExplode);
           $breedName= $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['settingType'=>'BREED_NAME','slug'=>$lastElement.'-breed','status'=>1]);
           $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$breedName));
           $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breedName));


           /**@var Setting $entity*/
           foreach ($entities as $entity){
               $arrayData['materials'][]=array('id'=>$entity->getId(),'text'=>$entity->getName());

           }
           /**@var Setting $specie*/
           foreach ($species as $specie){
               $arrayData['species'][]=array('id'=>$specie->getId(),'text'=>$specie->getName());
           }
       }

        return $arrayData;
    }

    /**
     * @Route("/materials/breed/{id}/ajax", methods={"POST"}, name="crm_farmer_training_material_ajax", options={"expose"=true})
     * @param $id
     * @return Response
     */
    public function getFarmerTrainingMaterialByBreedName($id): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'TRAINING_MATERIAL','parent'=>$id));
        $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$id));

        $arrayData = array();
        /**@var Setting $entity*/
        foreach ($entities as $entity){
                $arrayData['materials'][]=array('id'=>$entity->getId(),'text'=>$entity->getName());

        }
        /**@var Setting $specie*/
        foreach ($species as $specie){
                $arrayData['species'][]=array('id'=>$specie->getId(),'text'=>$specie->getName());
        }

        return new JsonResponse($arrayData);
    }

}
