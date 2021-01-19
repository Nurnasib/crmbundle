<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;

/**
 * @Route("/crm/customer")
 */

class CrmCustomerReportController extends AbstractController
{

    /**
     * @Route("/report", name="crm_customer_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index()
    {
        $chickLifeCycle = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/crmcustomer/report/report.html.twig',['chickLifeCycles' => $chickLifeCycle]);
    }
}