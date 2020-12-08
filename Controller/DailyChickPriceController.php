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
use App\Entity\Core\Agent;
use Dompdf\Dompdf;
use Dompdf\Options;
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
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DailyChickPrice;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Form\ChickLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/daily/chick/price")
 */
class DailyChickPriceController extends AbstractController
{
    /**
     * @param $report
     * @Route("/", methods={"GET"}, name="crm_chick")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function index( ): Response
    {

        $entities = $this->getDoctrine()->getRepository(DailyChickPrice::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/dailyChickPrice/index.html.twig',['entities' => $entities]);
    }

    /**
     * @param CrmCustomer $crmCustomer
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/location/{location}/new/modal", methods={"GET", "POST"}, name="daily_chick_price")
     */
    public function newModal(Request $request, Location $location): Response
    {

        $entity = new DailyChickPrice();
        $existReport = $this->getDoctrine()->getRepository(DailyChickPrice::class)->getExistingReportByDateEmployeeAndLocation($this->getUser(),$location);
        if ($existReport){
            $entity= $existReport;
            return $this->redirectToRoute('daily_chick_price_details_modal', ['id'=>$entity->getId()]);
        }


        $reportingDate = date('Y-m-d',strtotime('now'));

        $entity->setReportingDate(new \DateTime($reportingDate));
        $entity->setEmployee($this->getUser());
        $entity->setLocation($location);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

//        return new Response('success');

        return $this->redirectToRoute('daily_chick_price_details_modal', ['id'=>$entity->getId()]);

    }


    /**
     * @param DailyChickPrice $dailyChickPrice
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/details/{id}/modal", methods={"GET", "POST"}, name="daily_chick_price_details_modal")
     */
    public function dailyChickPriceDetailsModal(DailyChickPrice $dailyChickPrice): Response
    {
        $feeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_NAME'));
        $chickTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'CHICK_TYPE'));

        $crmChickLifeCycleDetails = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->findOneBy(array('crmDailyChickPrice'=>$dailyChickPrice->getId()));
        if (!$crmChickLifeCycleDetails){
            foreach($feeds as $feed){
                foreach($chickTypes as $chickType){
                    $dailyChickPriceDetail = new DailyChickPriceDetails();

                    $dailyChickPriceDetail->setFeed($feed);
                    $dailyChickPriceDetail->setChickType($chickType);

                    $dailyChickPriceDetail->setCrmDailyChickPrice($dailyChickPrice);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($dailyChickPriceDetail);

                    $em->flush();
                }
            }
        }

        $returnData = array();

        foreach ($dailyChickPrice->getCrmDailyChickPriceDetails() as $chickPriceDetail){
            $returnData[$chickPriceDetail->getFeed()->getId()][$chickPriceDetail->getChickType()->getId()]= $chickPriceDetail;
        }



        return $this->render('@TerminalbdCrm/dailyChickPrice/new-modal.html.twig', [
            'chickPriceDetail' => $returnData,
            'feeds' => $feeds,
            'chickTypes' => $chickTypes,
        ]);
    }



    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/details/{id}/edit", methods={"POST"}, name="crm_daily_chick_price_detail_update", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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

    /**
     * @param $report
     * @Route("/{report}", methods={"GET"}, name="crm_chick_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport( string $report): Response
    {

        $entities = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->getChickLifeCycleByReportType($report);
        return $this->render('@TerminalbdCrm/chickLifecycle/report/report.html.twig',['entities' => $entities]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report", methods={"GET"}, name="crm_chick_report_detail")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportDetails( ChickLifeCycle $chickLifeCycle): Response
    {

        return $this->render('@TerminalbdCrm/chickLifecycle/report/report-details.html.twig',['chickLifeCycle' => $chickLifeCycle]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report/pdf", methods={"GET"}, name="crm_chick_report_detail_pdf")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportPdf( ChickLifeCycle $chickLifeCycle): Response
    {

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@TerminalbdCrm/chickLifecycle/report/report-pdf.html.twig',['chickLifeCycle' => $chickLifeCycle]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($chickLifeCycle->getReport()->getSlug().".pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @param ChickLifeCycle $chickLifeCycle
     * @Route("/{id}/report/excel", methods={"GET"}, name="crm_chick_report_detail_excel")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function reportExcel( ChickLifeCycle $chickLifeCycle): Response
    {
        $html = $this->renderView('@TerminalbdCrm/chickLifecycle/report/report-excel.html.twig',['chickLifeCycle' => $chickLifeCycle]);

        $file=$chickLifeCycle->getReport()->getSlug().'_'.time().".xls";
        $test="$html";
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
//        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$file");
        echo $test;die;

    }


}
