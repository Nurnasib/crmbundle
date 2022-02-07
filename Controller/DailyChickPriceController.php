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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\DailyChickPrice;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/day-old-chick-price", name="day_old_chick_price")
 * @Security("is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_DEVELOPER')")
 */
class DailyChickPriceController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_chick")
     * @return Response
     */
    public function index( ): Response
    {

        $entities = $this->getDoctrine()->getRepository(DailyChickPrice::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/dailyChickPrice/index.html.twig',['entities' => $entities]);
    }

    /**
     * @throws \Exception
     * @Route("/new", methods={"GET", "POST"}, name="_new")
     */
    public function new()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $entity = new DailyChickPrice();
        $existReport = $this->getDoctrine()->getRepository(DailyChickPrice::class)->getExistingReportByDateAndEmployee($this->getUser());
        if (!$existReport){
            $entity->setReportingDate(new \DateTime("now"));
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }else{
            $entity = $existReport;
        }

        $feeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'HATCHERY', 'status' => 1));
        $chickTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'CHICK_TYPE', 'status' => 1));

        $parent = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->findOneBy(array('crmDailyChickPrice' => $entity));

        if (!$parent){
            foreach($feeds as $feed){
                foreach($chickTypes as $chickType){
                    $dailyChickPriceDetail = new DailyChickPriceDetails();

                    $dailyChickPriceDetail->setFeed($feed);
                    $dailyChickPriceDetail->setChickType($chickType);

                    $dailyChickPriceDetail->setCrmDailyChickPrice($entity);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($dailyChickPriceDetail);

                    $em->flush();
                }
            }
        }

        $returnData = array();
        $results = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->findBy(array('crmDailyChickPrice' => $entity));

        foreach ($results as $result){
            $returnData[$result->getFeed()->getId()][$result->getChickType()->getId()] = $result;
        }
        return $this->render('@TerminalbdCrm/dailyChickPrice/new.html.twig', [
            'chickPriceDetail' => $returnData,
            'feeds' => $feeds,
            'chickTypes' => $chickTypes,
        ]);

    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/details/{id}/edit", methods={"POST"}, name="_update", options={"expose"=true})
     * @param Request $request
     * @param DailyChickPriceDetails $entity
     * @return Response
     */

    public function editDailyChickDetails(Request $request, DailyChickPriceDetails $entity): Response
    {
        $data = $request->request->all();

        $entity->setPrice(isset($data['price'])&&$data['price']!=""?$data['price']:0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'price'=>$entity->getPrice(),
                'status'=>200,
            )
        );

    }

}
