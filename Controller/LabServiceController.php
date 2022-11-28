<?php

namespace Terminalbd\CrmBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarmMedicineOrVaccineCost;
use Terminalbd\CrmBundle\Entity\BroilerLifeCycle;
use Terminalbd\CrmBundle\Entity\ComplainDifferentProduct;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;
use Terminalbd\CrmBundle\Entity\LabService;
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ComplainDifferentProductFormType;
use Terminalbd\CrmBundle\Form\DiseaseMappingFormType;


/**
 * @Route("/crm/lab/service")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER')")
 */
class LabServiceController extends AbstractController
{
    /**
     * @Route("/{breed_name}/new/modal", methods={"GET", "POST"}, name="lab_service_new_modal", options={"expose"=true})
     * @param Request $request
     * @param $breed_name
     * @return Response
     */
    public function newModal(Request $request, $breed_name): Response
    {
        $em = $this->getDoctrine()->getManager();

//        $entity = new LabService();
        $user = $this->getUser();
        $userLabIds = $user->getLabs();
        if($userLabIds && count($userLabIds)>0){
            $labNames = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'LAB_NAME', 'id'=>$userLabIds));
            $labServices = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'LAB_SERVICE_NAME'));
            foreach ($labNames as $lab){

                foreach ($labServices as $labService){

                    $exitingFcrDifferentCompany = $this->getDoctrine()->getRepository(LabService::class)->getExitingCheckLabServiceByCreatedDateEmployeeAndCompany($this->getUser(), $lab, $labService, $breed_name);

                    if(!$exitingFcrDifferentCompany){

                        $entity = new LabService();

                        $entity->setBreedName($breed_name);
                        $entity->setLab($lab);
                        $entity->setService($labService);
                        $entity->setEmployee($this->getUser());
                        $entity->setReportingYear(date('Y'));

                        $em->persist($entity);
                        $em->flush();

                        $dataArray[]=$entity->getId();
                    }
                }

            }

            $allLabServices = $this->getDoctrine()->getRepository(LabService::class)->getLabServiceByCreatedDateAndEmployee($this->getUser(), $breed_name);

            return $this->render('@TerminalbdCrm/labService/new-modal.html.twig', [
                'services' => $labServices,
                'labs' => $labNames,
                'labServices' => $allLabServices,
                'user' => $this->getUser(),
            ]);
        }
        $this->addFlash('error', 'This user is not available lab');
        return $this->redirectToRoute('dashboard');


    }


    /**
     * @Route("/{id}/edit", methods={"POST"}, name="lab_service_edit", options={"expose"=true})
     * @param Request $request
     * @param LabService $entity
     * @return Response
     */

    public function editLabServiceDetails(Request $request, LabService $entity): Response
    {
        $data = $request->request->all();
        $metaKey = $data['dataMetaKey'];
        $metaValue = $data['dataMetaValue'];
        $metaKey = ucfirst($metaKey);

        if($metaKey!=''&&$metaValue!=''){

            $set = 'set'.$metaKey;

            $entity->$set($metaValue);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $get = 'get'.$metaKey;

        $value = $entity->$get($metaValue);

        return new JsonResponse(
            array(
                'success'=>'Success',
                'value'=>$value,
                'status'=>200,
            )
        );

    }

}
