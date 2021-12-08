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
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\FishLifeCycle;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\FishSalesPrice;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\TilapiaFrySales;
use Terminalbd\CrmBundle\Form\FishLifeCycleDetailsFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Terminalbd\CrmBundle\Form\DairyLifeCycleDetailsFormType;


/**
 * @Route("/crm/tilapia/fry/sales")
 */
class TilapiaFrySalesController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="tilapia_fry_sales_add", options={"expose"=true})
     */
    public function newModal(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $feeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1, 'settingType'=>'FEED_NAME'),array('name' => 'ASC'));
        $agents = $this->getDoctrine()->getRepository(Agent::class)->getLocationWise($this->getUser());

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
            }
        }

        $nourishTilapiaFrySales = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getTilapiaFrySalesByEmployeeMonthYear( $this->getUser(), $arrayMonth, $currentYear);
        $competitorTilapiaFrySales = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getCompetitorsTilapiaFrySalesByEmployeeMonthYear( $this->getUser(), $arrayMonth, $currentYear);

        return $this->render('@TerminalbdCrm/tilapiaFrySales/new-modal.html.twig', [
            'user' => $this->getUser(),
            'arrayMonth' => $arrayMonth,
            'yearRange' => $yearRange,
            'arrayMonthRange' => $arrayMonthRange,
            'feeds' => $feeds,
            'currentYear' => $currentYear,
            'agents' => $agents,
            'allTilapiaFrySales' => $nourishTilapiaFrySales,
            'competitorsTilapiaFrySales' => $competitorTilapiaFrySales,
        ]);
    }

    /**
     * @Route("/data/insert", methods={"POST"}, name="tilapia_fry_sales_data_insert", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function tilapiaFrySalesQuantityUpdate(Request $request): Response
    {
        $data = $request->request->all();
        $agentId = $data['agent_id'];
        $month = $data['month'];
        $quantity = $data['quantity'];
        $currentYear = date('Y');
        $employee=$this->getUser();
        $agent=null;
        if($agentId){
            $agent=$this->getDoctrine()->getRepository(Agent::class)->find($agentId);
        }

        $existingEntity = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getExitingCheckTilapiaFrySalesByCreatedDateEmployeeAgentMonthYear($employee, $month, $currentYear, $agent, TilapiaFrySales::TILAPIA_FRY_SALES_NOURISH);

        $entity= new TilapiaFrySales();
        if($existingEntity){
            $entity=$this->getDoctrine()->getRepository(TilapiaFrySales::class)->find($existingEntity['id']);
        }

        $entity->setMonthName($month?$month:null);
        $entity->setYear($currentYear);
        $entity->setQuantity($quantity?$quantity:0);
        $entity->setEmployee($employee);
        $entity->setCreatedAt(new \DateTime());
        $entity->setAgent($agent);
        $entity->setType(TilapiaFrySales::TILAPIA_FRY_SALES_NOURISH);

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
     * @Route("/competitor/data/insert", methods={"POST"}, name="competitor_tilapia_fry_sales_data_insert", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function competitorTilapiaFrySalesQuantityInsert(Request $request): Response
    {
        $data = $request->request->all();
        $agentId = $data['agent_id'];
        $feedId = $data['feed_id'];
        $month = $data['month'];
        $quantity = $data['quantity'];
        $currentYear = date('Y');
        $employee=$this->getUser();
        $agent=null;
        if($agentId){
            $agent=$this->getDoctrine()->getRepository(Agent::class)->find($agentId);
        }
        $feed=null;
        if($feedId){
            $feed=$this->getDoctrine()->getRepository(Setting::class)->find($feedId);
        }

        $existingEntity = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getExitingCheckTilapiaFrySalesByCreatedDateEmployeeAgentFeedMonthYear($employee, $month, $currentYear, $agent, $feed, TilapiaFrySales::TILAPIA_FRY_SALES_OTHER);

        $entity= new TilapiaFrySales();
        if($existingEntity){
            $entity=$this->getDoctrine()->getRepository(TilapiaFrySales::class)->find($existingEntity['id']);
        }

        $entity->setMonthName($month?$month:null);
        $entity->setYear($currentYear);
        $entity->setQuantity($quantity?$quantity:0);
        $entity->setEmployee($employee);
        $entity->setCreatedAt(new \DateTime());
        $entity->setAgent($agent);
        $entity->setFeed($feed);
        $entity->setType(TilapiaFrySales::TILAPIA_FRY_SALES_OTHER);

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'id'=>$feedId,
                'status'=>200,
            )
        );

    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/refresh", methods={"GET"}, name="crm_tilapia_fry_sales_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function tilapiaFrySalesRefresh(): Response
    {

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
            }
        }

        $allTilapiaFrySales = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getTilapiaFrySalesByEmployeeMonthYear( $this->getUser(), $arrayMonth, $currentYear);

        return $this->render('@TerminalbdCrm/tilapiaFrySales/_content_table_body.html.twig', [
            'allTilapiaFrySales' => $allTilapiaFrySales,
            'arrayMonth' => $arrayMonth,
        ]);
    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     * @Route("/competitor/refresh", methods={"GET"}, name="crm_competitor_tilapia_fry_sales_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function competitorTilapiaFrySalesRefresh(): Response
    {

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
            }
        }

        $competitorTilapiaFrySales = $this->getDoctrine()->getRepository(TilapiaFrySales::class)->getCompetitorsTilapiaFrySalesByEmployeeMonthYear( $this->getUser(), $arrayMonth, $currentYear);

        return $this->render('@TerminalbdCrm/tilapiaFrySales/_competitor_content_table_body.html.twig', [
            'competitorsTilapiaFrySales' => $competitorTilapiaFrySales,
            'arrayMonth' => $arrayMonth,
            'arrayMonthRange' => $arrayMonthRange,
        ]);
    }


}
