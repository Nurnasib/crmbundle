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
use Terminalbd\CrmBundle\Form\AntibioticFreeFarmFormType;
use Terminalbd\CrmBundle\Form\BroilerLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\ComplainDifferentProductFormType;
use Terminalbd\CrmBundle\Form\DiseaseMappingFormType;


/**
 * @Route("/crm/fcr/differnt/company")
 */
class FcrDifferentCompanyController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{breed_name}/new/modal", methods={"GET", "POST"}, name="fcr_different_company_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, $breed_name): Response
    {
        $em = $this->getDoctrine()->getManager();

//        $entity = new FcrDifferentCompanies();

        $hatcheries = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'HATCHERY'));

        foreach ($hatcheries as $hatchery){

            $exitingFcrDifferentCompany = $this->getDoctrine()->getRepository(FcrDifferentCompanies::class)->getExitingCheckFcrDifferentCompanyByCreatedDateEmployeeAndCompany($this->getUser(), $hatchery, $breed_name);
            if(!$exitingFcrDifferentCompany){

                $entity = new FcrDifferentCompanies();

                $entity->setBreedName($breed_name);
                $entity->setHatchery($hatchery);
                $entity->setEmployee($this->getUser());

                $em->persist($entity);
                $em->flush();
            }
        }

        $fcrDifferentCompanies = $this->getDoctrine()->getRepository(FcrDifferentCompanies::class)->getFcrDifferentCompanyByCreatedDateAndEmployee($this->getUser(), $breed_name);


        return $this->render('@TerminalbdCrm/fcrDifferentCompany/new-modal.html.twig', [
            'hatcheries' => $hatcheries,
//            'entity' => $entity,
            'fcrDifferentCompanies' => $fcrDifferentCompanies,
        ]);
    }


    /**
     * @Route("/{id}/edit", methods={"POST"}, name="fcr_different_company_edit", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function editLifeCycleDetails(Request $request, FcrDifferentCompanies $entity): Response
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
