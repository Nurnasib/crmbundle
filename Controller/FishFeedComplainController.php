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
use Terminalbd\CrmBundle\Entity\ComplainDifferentProduct;
use Terminalbd\CrmBundle\Entity\FishFeedComplain;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Form\FishFeedComplainFormType;
use Terminalbd\CrmBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/crm/fish/feed/complain")
 */
class FishFeedComplainController extends AbstractController
{
    /**
     * @param CrmCustomer $crmCustomer
     * @ParamConverter("crmCustomer", class="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="fish_feed_complain_new_modal", options={"expose"=true})
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new FishFeedComplain();
        $feedMills = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'FEED_MILL'));

        $arrayMonth=[];
        $arrayMonthRange=[];
        $currentYear = date('Y');
        $yearRange[]=$currentYear;

        for($i=1; $i<=12; $i++){
            $monthDigit = date('m', mktime(0, 0, 0, $i, 10));
            $month = date('F', mktime(0, 0, 0, $i, 10));
            $currentMonth = date('m');
            $arrayMonth[]=$month;
        }
        $form = $this->createForm(FishFeedComplainFormType::class, $entity,array('report' => $report));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setEmployee($this->getUser());
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array(
                'id'=> $entity->getId(),
                'status'=> 'new'
            ));
        }
        $complains=$this->getDoctrine()->getRepository(FishFeedComplain::class)->getFishFeedComplainByDateAndEmployee($this->getUser());
        return $this->render('@TerminalbdCrm/fishFeedComplain/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'arrayMonth' => $arrayMonth,
            'yearRange' => $yearRange,
            'feedMills' => $feedMills,
            'currentYear' => $currentYear,
            'complains' => $complains,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @param FishFeedComplain $fishFeedComplain
     * @Route("/refresh", methods={"GET"}, name="crm_fish_feed_complain_refresh", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function fishFeedComplainRefresh(): Response
    {
        $complains=$this->getDoctrine()->getRepository(FishFeedComplain::class)->getFishFeedComplainByDateAndEmployee($this->getUser());

        return $this->render('@TerminalbdCrm/fishFeedComplain/details-modal.html.twig', [
            'complains' => $complains,
        ]);
    }


}
