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
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\FishLifeCycle;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FishSalesPrice;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\FishLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/fish/sales/price")
 */
class FishSalesPriceController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="fish_sales_price_add", options={"expose"=true})
     */
    public function newModal(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breedNameObj = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('status'=>1, 'settingType'=>'BREED_NAME','slug'=>'fish-breed'));

        $speciesTypesByParent = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breedNameObj));


        $fishSizes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FISH_SIZE'));
        $arrayMonth=[];
        $arrayMonthRange=[];
        $currentYear = date('Y');
        $yearRange[]=$currentYear;
        $arrFishSizes=[];
        foreach ($fishSizes as $fishSize){
            $arrFishSizes[$fishSize->getParent()->getId()][]=$fishSize;
        }

        for($i=1; $i<=12; $i++){
            $monthDigit = date('m', mktime(0, 0, 0, $i, 10));
            $month = date('F', mktime(0, 0, 0, $i, 10));
            $currentMonth = date('m');

            $arrayMonth[]=$month;
            if($currentMonth==$monthDigit || $currentMonth-1==$monthDigit){
                $arrayMonthRange[]=$month;
                /* @var Setting $fishSize*/
                foreach ($fishSizes as $fishSize){
                    $exitingCompanyWiseFeedSale = $this->getDoctrine()->getRepository(FishSalesPrice::class)->findOneBy(array('year'=>$currentYear, 'monthName'=>$month, 'employee'=>$this->getUser(), 'fishSize'=>$fishSize));
                    if(!$exitingCompanyWiseFeedSale){
                        $fishSalesPrice= new FishSalesPrice();
                        $fishSalesPrice->setSpeciesType($fishSize->getParent());
                        $fishSalesPrice->setFishSize($fishSize);
                        $fishSalesPrice->setMonthName($month);
                        $fishSalesPrice->setYear($currentYear);
                        $fishSalesPrice->setEmployee($this->getUser());
                        $em->persist($fishSalesPrice);
                        $em->flush();
                    }

                }
            }
        }

        $allFishSalesPrice = $this->getDoctrine()->getRepository(FishSalesPrice::class)->getFishSalesPriceByCreatedDateAndEmployee( $yearRange, $arrayMonthRange, $this->getUser());

        return $this->render('@TerminalbdCrm/fishSalesPrice/new-modal.html.twig', [
            'user' => $this->getUser(),
            'arrayMonth' => $arrayMonth,
            'allFishSalesPrice' => $allFishSalesPrice,
            'yearRange' => $yearRange,
            'arrayMonthRange' => $arrayMonthRange,
            'speciesTypes' => $speciesTypesByParent,
            'fishSizes' => $arrFishSizes,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * @Route("/{id}/update", methods={"POST"}, name="fish_sales_price_data_update", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function fishSalesPriceUpdate(Request $request, FishSalesPrice $entity): Response
    {
        $data = $request->request->all();
        $price = $data['price'];

        if($price!=''){
            $entity->setPrice($price);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'id'=>$entity->getId(),
                'status'=>200,
            )
        );

    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/{id}/refresh", methods={"GET"}, name="crm_fish_life_cycle_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function fishLifeCycleReportRefresh(FishLifeCycle $fishLifeCycle): Response
    {
        $fishLifeCycleDetailsByReportingMonth = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->getFishLifeCycleDetailsByReportingDateAndEmployee($fishLifeCycle->getReport(), $this->getUser());

        return $this->render('@TerminalbdCrm/fishLifeCycle/partial/fish-life-cycle-details-body.html.twig', [
            'fishLifeCycle' => $fishLifeCycle,
            'fishLifeCycleDetailsByReportingMonth' => $fishLifeCycleDetailsByReportingMonth,
        ]);
    }


}
