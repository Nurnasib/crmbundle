<?php


namespace Terminalbd\CrmBundle\Controller\Report;



use App\Entity\Core\Setting;
use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class VisitReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 */
class VisitReportController extends AbstractController
{
    /**
     * @Route("/crm/visit-report", defaults={"mode" = null}, name="visit_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function visits(Request $request)
    {
        $entities = [];
        $startDate = null;
        $endDate = null;
        $employee = null;
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $startDate = $form->getData()['startDate'] ? new \DateTime($form->getData()['startDate']) : new \DateTime('now');
            $endDate = $form->getData()['endDate'] ? new \DateTime($form->getData()['endDate']) : new \DateTime('now');

            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');

            $employee = $form->getData()['employee'];

            $entities = $this->getDoctrine()->getRepository(CrmVisit::class)->getVisits($startDate, $endDate, $employee, $this->getUser());
        }
        return $this->render('@TerminalbdCrm/report/visit/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'employee' => $employee,
        ]);
    }

    /**
     * @Route("/crm/visit-details-report", name="visit_details_report")
     * @param Request $request
     * @throws \Exception
     */
    public function visitDetails(Request $request)
    {
        $mode = $request->query->get('mode');
        $employeeId = $request->query->get('employeeId');

        $visitDate = $request->query->get('visitDate');
        $begin = (new \DateTime($visitDate))->format('Y-m-d 00:00:00');
        $end = (new \DateTime($visitDate))->format('Y-m-d 23:59:59');

        if ($request->query->get('startDate') && $request->query->get('endDate')){
            $begin = $request->query->get('startDate');
            $end = $request->query->get('endDate');
        }
        $entities = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->getVisitDetails($begin,$end,$employeeId);
//        dd($employeeId);
        if ($mode == 'pdf'){

            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial, sans-serif');

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('@TerminalbdCrm/report/visit/pdf.html.twig',['entities' => $entities]);

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser (force download)
            $dompdf->stream($request->get('_route') . ".pdf", [
                "Attachment" => false
            ]);
        }

    }

    /**
     * @Route("/visit-status", name="visit_status")
     */
    public function visitStatus(Request $request)
    {
        $employees = null;
        $firstDayOfMonth = null;
        $lastDayOfMonth = null;
        $selectedEmployee = null;
        $visitStatus = null;
        
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()){

            $selectedEmployee = $form->get('employee')->getData();
            $month = $form->get('month')->getData();
            $year = $form->get('year')->getData();

            if ($month && $year){
                $firstDayOfMonth = date('Y-m-d', strtotime("01-$month-$year"));
                $lastDayOfMonth = date('Y-m-t', strtotime("01-$month-$year"));

                $group = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['slug' => 'employee']);
                
                $roleSplitArray = [];
                $userRoles = [];

                foreach ($this->getUser()->getRoles() as $role) {
                    $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
                }
                if (in_array('ADMIN', $roleSplitArray)) {
                    if (in_array('ROLE_CRM_POULTRY_ADMIN', $this->getUser()->getRoles())) {
                        array_push($userRoles, 'ROLE_CRM_POULTRY_USER');
                    }
                    if (in_array('ROLE_CRM_CATTLE_ADMIN', $this->getUser()->getRoles())) {
                        array_push($userRoles, 'ROLE_CRM_CATTLE_USER');
                    }
                    if (in_array('ROLE_CRM_AQUA_ADMIN', $this->getUser()->getRoles())) {
                        array_push($userRoles, 'ROLE_CRM_AQUA_USER');
                    }
                    if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $this->getUser()->getRoles())) {
                        array_push($userRoles, 'ROLE_CRM_SALES_MARKETING_USER');
                    }
                }

                $employees = $this->getDoctrine()->getRepository(User::class)->getServiceModeWiseEmployee($userRoles);

                $visitStatus = $this->getDoctrine()->getRepository(CrmVisit::class)->getVisitsStatus($firstDayOfMonth, $lastDayOfMonth, $selectedEmployee, $this->getUser());
            }

        }
        
        return $this->render("@TerminalbdCrm/report/visit-status/index.html.twig",[
            'employees' => $employees,
            'visitStatus' => $visitStatus,
            'firstDayOfMonth' => $firstDayOfMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'selectedEmployee' => $selectedEmployee,
            'form' => $form->createView(),
            
        ]);
    }
}