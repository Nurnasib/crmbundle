<?php

namespace Terminalbd\CrmBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarmMedicineOrVaccineCost;
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/company/wise/feed/sale")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_DEVELOPER') or is_granted('ROLE_CRM_SALES_MARKETING_USER')")
 */
class CompanyWiseFeedSaleController extends AbstractController
{
    /**
     * @Route("/{breed_name}/new", methods={"GET", "POST"}, name="company_wise_feed_sale_new", options={"expose"=true})
     * @param Request $request
     * @param $breed_name
     * @return Response
     */
    public function newModal(Request $request, $breed_name): Response
    {
        $breedParam=$breed_name;
        $breedExplode= explode('-',$breed_name);
        $breed_name=isset($breedExplode[0])?$breedExplode[0]:'';
        $breedType=isset($breedExplode[1])?$breedExplode[1]:null;
        $em = $this->getDoctrine()->getManager();

        $breedNameObj = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('status'=>1, 'settingType'=>'BREED_NAME','slug'=>$breed_name.'-breed'));

        $farmTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FARM_TYPE','parent'=>$breedNameObj));

        $farmTypeId = [];
        if($farmTypesByParent){
            foreach ($farmTypesByParent as $value){
                $farmTypeId[]= $value->getId();
            }
        }

        $productsName = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeWithOutChickByBreedName($farmTypeId);

        if($breedType=='boiler'){
            $productsName = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeForBoilerChickByBreedName($farmTypeId);
        }
        if($breedType=='layer'){
            $productsName = $this->getDoctrine()->getRepository(Setting::class)->getProductTypeForLayerChickByBreedName($farmTypeId);
        }

        $feedCompanies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FEED_NAME'));

        if($breedType=='boiler' || $breedType=='layer'){
            $feedCompanies = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'HATCHERY'));
        }

        $arrayMonth=[];
        $arrayMonthRange=[];
        $currentYear = date('Y');
        $yearRange[]=$currentYear;

        for($i=1; $i<=12; $i++){
            $monthDigit = date('m', mktime(0, 0, 0, $i, 10));
            $month = date('F', mktime(0, 0, 0, $i, 10));
            $currentMonth = date('m');

            $arrayMonth[]=$month;
            if($currentMonth==$monthDigit || $currentMonth-1==$monthDigit){
                $arrayMonthRange[]=$month;
                foreach ($feedCompanies as $feedCompany){
                    $exitingCompanyWiseFeedSale = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getExitingCheckCompanyWiseFeedSaleByMonthYearEmployeeAndCompany($currentYear, $month, $feedCompany, $this->getUser(), $breedParam);
                    if(!$exitingCompanyWiseFeedSale){
                        $companyWiseFeedSale= new CompanyWiseFeedSale();
                        $companyWiseFeedSale->setBreedName($breedParam);
                        $companyWiseFeedSale->setEmployee($this->getUser());
                        $companyWiseFeedSale->setFeedCompany($feedCompany);
                        $companyWiseFeedSale->setMonthName($month);
                        $companyWiseFeedSale->setYear($currentYear);

                        $em->persist($companyWiseFeedSale);
                        $em->flush();
                    }

                }
            }
        }

        $allFeedSales = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->getCompanyWiseFeedSaleByCreatedDateAndEmployee( $yearRange, $arrayMonthRange, $this->getUser(), $breedParam);

        return $this->render('@TerminalbdCrm/companyWiseFeedSale/new-modal.html.twig', [
            'productsName' => $productsName,
            'user' => $this->getUser(),
            'arrayMonth' => $arrayMonth,
            'allFeedSales' => $allFeedSales,
            'yearRange' => $yearRange,
            'arrayMonthRange' => $arrayMonthRange,
            'feedCompanies' => $feedCompanies,
            'breedType' => $breedType,
        ]);
    }


    /**
     * @Route("/{id}/edit", methods={"POST"}, name="company_wise_feed_sale_edit", options={"expose"=true})
     */

    public function editCompanyWiseFeedSale(Request $request, CompanyWiseFeedSale $entity): Response
    {
        $data = $request->request->all();
        $metaValue = $data['dataMetaValue'];

        $jsonValue = json_encode(array_filter($metaValue, 'strlen'));

        $entity->setProductWiseQty($jsonValue);

        $entity->setTotalQty(array_sum(array_filter($metaValue, 'strlen')));

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'status'=>200,
            )
        );

    }

}
