<?php


namespace Terminalbd\CrmBundle\Controller\Report;



use App\Entity\Core\Setting;
use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;
use Terminalbd\CrmBundle\Form\SearchFilterFormTypeForVisitReport;
use Terminalbd\CrmBundle\Form\SearchFilterFormTypeForVisitSummeryReport;
use Terminalbd\KpiBundle\Entity\AgentCategory;
use DateTime;
use DatePeriod;
use DateInterval;

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
        $serviceMode = null;
        $process = null;
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormTypeForVisitReport::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $requestData = $request->request->all();

            $startDate = $form->getData()['startDate'] ? new \DateTime($form->getData()['startDate']) : new \DateTime('now');
            $endDate = $form->getData()['endDate'] ? new \DateTime($form->getData()['endDate']) : new \DateTime('now');

            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');

            $employee = $form->getData()['employee'];
            $serviceMode = $form->getData()['serviceMode'];
            $process = $form->getData()['process'];
            if(!$employee && !$serviceMode){
                $this->addFlash('error', 'Employee Or Service mode is required.');
                return $this->redirectToRoute('visit_report');
            }

            
            $entities = $this->getDoctrine()->getRepository(CrmVisit::class)->getVisits($startDate, $endDate, $employee, $serviceMode, $this->getUser(), $process);

            if(isset($requestData['pdf_download'])&&$requestData['pdf_download']=='pdf_download'){
                $pdfOptions = new Options();
                $pdfOptions->set('defaultFont', 'Arial, sans-serif');

                // Instantiate Dompdf with our options
                $dompdf = new Dompdf($pdfOptions);

                // Retrieve the HTML generated in our twig file
                $html = $this->renderView('@TerminalbdCrm/report/visit/visit-pdf.html.twig',['entities' => $entities]);

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
        return $this->render('@TerminalbdCrm/report/visit/index.html.twig',[
            'form' => $form->createView(),
            'entities' => $entities,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'employee' => $employee,
            'serviceMode' => $serviceMode,
            'process' => $process,
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
        $serviceModeId = $request->query->get('serviceModeId');
        $process = $request->query->get('process');

        $visitDate = $request->query->get('visitDate');
        $begin = (new \DateTime($visitDate))->format('Y-m-d 00:00:00');
        $end = (new \DateTime($visitDate))->format('Y-m-d 23:59:59');

        if ($request->query->get('startDate') && $request->query->get('endDate')){
            $begin = $request->query->get('startDate');
            $end = $request->query->get('endDate');
        }
        $entities = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->getVisitDetails($begin,$end,$employeeId,$serviceModeId, $process);
//        dd($entities);
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
        if($mode=='excel'){
            return $this->render('@TerminalbdCrm/report/visit/excel.html.twig',[
                'entities' => $entities,
            ]);
        }
        return true;
    }

    /**
     * @Route("/crm/visit-status", name="visit_status")
     */
    public function visitStatus(Request $request)
    {
        $selectedEmployee = null;
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $selectedLineManagerUserId = null;
        $roleSplitArray = [];
        $userRoles = [];
        $lineManagersId = [];

        $employees=[];

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
            $employees = $this->getDoctrine()->getRepository(User::class)->getRoleWiseEmployees($userRoles);
        }elseif (in_array('ROLE_LINE_MANAGER', $this->getUser()->getRoles())){
            $employees = $this->getDoctrine()->getRepository(User::class)->getEmployeesByEmployeeIds($this->getUser());
        }

        $employeeIds=[];
        if($employees && isset($employees['employee'])){
            foreach ($employees['employee'] as $employee){
                $employeeIds[]=$employee['id'];
            }
        }
        if($employees && isset($employees['lineManager'])){
            foreach ($employees['lineManager'] as $lineManagerId=>$lineManagerName){
                $lineManagersId[]=$lineManagerId;
            }
        }
//        $lineManagers = $this->getDoctrine()->getRepository(User::class)->getAdminWiseLineManagers($userRoles);
        $lineManagers = $employees && isset($employees['lineManager'])?$employees['lineManager']:[];
        /*foreach ($lineManagers as $lineManager){
            $lineManagersId[] = $lineManager['userId'];
        }*/
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $selectedLineManagerUserId = $request->request->get('lineManager');

            $selectedEmployee = $form->get('employee')->getData();
            $month = $form->get('month')->getData() ? $form->get('month')->getData() : date('m');
            $year = $form->get('year')->getData() ? $form->get('year')->getData() : date(('Y'));

            //            if (!$selectedLineManagerUserId && !$selectedEmployee){
//                foreach ($lineManagers as $lineManager){
//                    $lineManagersId[] = $lineManager['userId'];
//                }
//            }

            if ($selectedLineManagerUserId && !$selectedEmployee){
                $lineManagersId = [];
                $lineManagersId[] = $selectedLineManagerUserId;
            }elseif ($selectedEmployee && !$selectedLineManagerUserId){
                $lineManagersId = [];
                $lineManagersId[] = $selectedEmployee->getLineManager()->getUserId();
            }elseif ($selectedEmployee && $selectedLineManagerUserId){
                if ($selectedEmployee->getLineManager()->getUserId() == $selectedLineManagerUserId){
                    $lineManagersId = [];
                    $lineManagersId[] = $selectedLineManagerUserId;
                }
            }

            if ($month && $year){
                $firstDayOfMonth = date('Y-m-d', strtotime("01-$month-$year"));
                $lastDayOfMonth = date('Y-m-t', strtotime("01-$month-$year"));
            }
        }

        $employeesByLineManager = $this->getDoctrine()->getRepository(User::class)->getLineManagerTeamMember($lineManagersId, $userRoles);
//        $employees = $this->getDoctrine()->getRepository(User::class)->getServiceModeWiseEmployee($lineManagersId, $userRoles);
        $visitStatus = $this->getDoctrine()->getRepository(CrmVisit::class)->getVisitsStatus($firstDayOfMonth, $lastDayOfMonth, $selectedEmployee, $lineManagersId, $employeeIds);
//dd($firstDayOfMonth);
        return $this->render("@TerminalbdCrm/report/visit-status/index.html.twig",[
            'employeesByLineManager' => $employeesByLineManager,
            'visitStatus' => $visitStatus,
            'firstDayOfMonth' => $firstDayOfMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'selectedEmployee' => $selectedEmployee,
            'lineManagers' => $lineManagers,
            'selectedLineManagerUserId' => $selectedLineManagerUserId,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/crm/agent-visit-monitor", name="agent_visit_monitor")
     */
    public function agentVisitMonitor(Request $request)
    {
        $selectedEmployee = null;
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormTypeForVisitReport::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        $records = [];
        $agentSales=[];
        $agentType = 'all';
        if ($form->isSubmitted()){
            $selectedEmployee = $form->getData()['employee'];
            $agentType = $form->getData()['agentType'] ?: 'all';
            $startDate = $form->getData()['startDate'] ? new \DateTime($form->getData()['startDate']) : new \DateTime('now');
            $endDate = $form->getData()['endDate'] ? new \DateTime($form->getData()['endDate']) : new \DateTime('now');

            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');
          $records = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->getAgentVisitMonitors( $startDate, $endDate, $selectedEmployee, $agentType);
          $agentIds = isset($records['records']) && sizeof($records['records'])>0 ? array_keys($records['records']) : [];

           if( sizeof($agentIds) > 0 ){
               $agentSales = $this->getDoctrine()->getRepository(AgentCategory::class)->getAgentCategoryByAgentIds($agentIds, $startDate, $endDate);
           }

        }
        
        return $this->render("@TerminalbdCrm/report/visit-status/agent-visit-monitor.html.twig",[
            'records' => $records,
            'form' => $form->createView(),
            'selectedEmployee' => $selectedEmployee,
            'agentSales' => $agentSales,
            'agentType' => $agentType,

        ]);
    }

    /**
     * @Route("/crm/agent-visit-monitor-date-wise", name="agent_visit_monitor_date_wise")
     */
    public function agentVisitMonitorDateWise(Request $request)
    {
        $selectedEmployee = null;
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormTypeForVisitReport::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        $records = [];
        $agentSales=[];
        $getAllDates=[];
        $agentType = 'all';
        if ($form->isSubmitted()){
            $selectedEmployee = $form->getData()['employee'];
            $agentType = $form->getData()['agentType'] ?: 'all';
            $startDate = $form->getData()['startDate'] ? new \DateTime($form->getData()['startDate']) : new \DateTime('now');
            $endDate = $form->getData()['endDate'] ? new \DateTime($form->getData()['endDate']) : new \DateTime('now');

            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');

            $getAllDates = $this->getDatesBetween($startDate, $endDate);

          $records = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->getAgentVisitMonitorsDateWise( $startDate, $endDate, $selectedEmployee, $agentType);
        }

        return $this->render("@TerminalbdCrm/report/visit-status/agent-visit-monitor-date-wise.html.twig",[
            'records' => $records,
            'form' => $form->createView(),
            'selectedEmployee' => $selectedEmployee,
//            'agentSales' => $agentSales,
            'allDates' => $getAllDates,
            'agentType' => $agentType,

        ]);
    }

    function getDatesBetween(string $startDate, string $endDate): array
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $interval = new DateInterval('P1D'); // 1-day interval
        $dates = [];

        while ($start <= $end) { // Ensure last date is included
            $dates[] = [
                'day'  => $start->format('l'),    // Example: "Wednesday"
                'date' => $start->format('d-m-Y') // Example: "26-02-2025"
            ];
            $start->add($interval);
        }

        return $dates;
    }

    /**
     * Employee Monthly Activity Report.
     *
     * Day-by-day activity grid for a single employee: visit counts split by agent /
     * sub agent / farmer / other agent, plus leave, office work and holiday flags.
     * Backed by CrmVisitRepository::getEmployeeMonthlyActivity(), which is used
     * nowhere else, so no other report is touched.
     *
     * @Route("/crm/employee-monthly-activity", name="employee_monthly_activity_report")
     */
    public function employeeMonthlyActivityReport(Request $request)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormTypeForVisitReport::class, null, [
            'loggedUser' => $this->getUser(), 'userRepo' => $userRepo
        ]);

        // All of the following act on this form instance only - the shared form type
        // itself is untouched, so the reports that share it keep their own fields.
        // The employee field is kept because it carries the role-based scoping that
        // limits a line manager to their own team.
        $form->remove('process');
        $form->remove('serviceMode');
        $form->remove('startDate');
        $form->remove('endDate');

        $monthChoices = [];
        foreach (range(1, 12) as $monthNumber) {
            $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
            $monthChoices[$monthName] = $monthName;
        }
        $yearChoices = [];
        foreach (range((int) date('Y'), 2020) as $yearNumber) {
            $yearChoices[$yearNumber] = $yearNumber;
        }

        $form->add('startMonth', ChoiceType::class, [
            'choices' => $monthChoices,
            'required' => false,
            'placeholder' => 'Select a Month',
            'data' => date('F'),
            'attr' => ['class' => 'form-control'],
        ]);
        $form->add('endMonth', ChoiceType::class, [
            'choices' => $monthChoices,
            'required' => false,
            'placeholder' => 'Select a Month',
            'data' => date('F'),
            'attr' => ['class' => 'form-control'],
        ]);
        $form->add('year', ChoiceType::class, [
            'choices' => $yearChoices,
            'required' => false,
            'placeholder' => 'Select Year',
            'data' => (int) date('Y'),
            'attr' => ['class' => 'form-control'],
        ]);

        $form->handleRequest($request);

        $selectedEmployee = null;
        $year = (int) date('Y');
        $startMonth = date('F');
        $endMonth = date('F');
        $blocks = [];

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $selectedEmployee = !empty($data['employee']) ? $data['employee'] : null;
            $year = !empty($data['year']) ? (int) $data['year'] : $year;
            $startMonth = !empty($data['startMonth']) ? $data['startMonth'] : $startMonth;
            $endMonth = !empty($data['endMonth']) ? $data['endMonth'] : $endMonth;
        }

        $startMonthNumber = (int) date('n', strtotime($startMonth . ' 1 ' . $year));
        $endMonthNumber = (int) date('n', strtotime($endMonth . ' 1 ' . $year));
        if ($endMonthNumber < $startMonthNumber) {
            // Guard against an end month earlier than the start month within the year.
            $endMonthNumber = $startMonthNumber;
            $endMonth = $startMonth;
        }

        $startDate = new DateTime(sprintf('%d-%02d-01', $year, $startMonthNumber));
        $endDate = new DateTime(date('Y-m-t', mktime(0, 0, 0, $endMonthNumber, 1, $year)));

        if ($form->isSubmitted() && $selectedEmployee) {
            $blocks = $this->getDoctrine()->getRepository(CrmVisit::class)
                ->getEmployeeMonthlyActivity($selectedEmployee->getId(), $startDate, $endDate);
        }

        return $this->render('@TerminalbdCrm/report/visit-status/employee-monthly-activity.html.twig', [
            'form' => $form->createView(),
            'blocks' => $blocks,
            'selectedEmployee' => $selectedEmployee,
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'year' => $year,
            'submitted' => $form->isSubmitted(),
        ]);
    }

    /**
     * @Route("/crm/visit-summery-report", name="visit_summery_report")
     */
    public function visitSummeryReport(Request $request)
    {
        $selectedEmployee = null;

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(SearchFilterFormTypeForVisitSummeryReport::class, null, ['loggedUser' => $this->getUser(),'userRepo'=>$userRepo]);
        $form->handleRequest($request);
        $records = [];
        $startDate = @strtotime(date('F') . ' ' . (int)date('Y'));
        $endDate = @strtotime(date('F') . ' ' . (int)date('Y'));

        $months = $this->monthRange($startDate, $endDate);
//        dd($months);
        $data=[];

        $filterBy = [
            'loggedUser' => $this->getUser(),
            'startMonth' => date('F'),
            'endMonth' => date('F'),
            'year' => (int)date('Y'),
        ];
        $employees=[];

        if ($form->isSubmitted()){
            $startDate = @strtotime($form->get('startMonth')->getData() . ' ' . $form->get('year')->getData());
            $endDate = @strtotime($form->get('endMonth')->getData() . ' ' . $form->get('year')->getData());
            $months = $this->monthRange($startDate, $endDate);

            $requestData = $form->getData();
            $lineManager = isset($requestData['lineManager']) && $requestData['lineManager'] != '' ? $requestData['lineManager'] : null;
            $employee = isset($requestData['employee']) && $requestData['employee'] != '' ? $requestData['employee'] : null;
            $reportMode = isset($requestData['reportMode']) && count($requestData['reportMode']) > 0 ? $requestData['reportMode'] : null;

            $filterBy['employee'] = $employee;
            $filterBy['lineManager'] = $lineManager;
            $filterBy['reportMode'] = $reportMode;
            $filterBy['year'] = (int)$form->get('year')->getData();
            $filterBy['months'] = $months;
            $filterBy['startMonth'] = $form->get('startMonth')->getData();
            $filterBy['endMonth'] = $form->get('endMonth')->getData();

            $employeeArray=[];
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
                $employeeArray = $this->getDoctrine()->getRepository(User::class)->getRoleWiseEmployees($userRoles);
            }elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $this->getUser()->getRoles())){
                $employeeArray = $this->getDoctrine()->getRepository(User::class)->getEmployeesByEmployeeIds($this->getUser());
            }
            if($lineManager){
                $employeeArray = $this->getDoctrine()->getRepository(User::class)->getEmployeesByEmployeeIds($lineManager);
            }
            if($employee){
                if($employee->getLineManager()){
                    $employeeArray = $this->getDoctrine()->getRepository(User::class)->getEmployeesByEmployeeIds($employee->getLineManager(), $employee->getId());
                }else{
                    // employee has no line manager (non-KPI account), nothing to report on
                    $employeeArray = [];
                }
            }

            $uniqueEmployees = [];
            if(isset($employeeArray['employee']) && sizeof($employeeArray['employee'])>0){
                $uniqueEmployees = $this->unique_array($employeeArray['employee'], 'id');
            }
            if($reportMode && sizeof($uniqueEmployees)>0){
                $reportModeIds = [];
                foreach ($reportMode as $rm) {
                    $reportModeIds[] = $rm->getId();
                }
                $allowedIds = $userRepo->getEmployeeIdsByReportMode(array_column($uniqueEmployees, 'id'), $reportModeIds);
                $uniqueEmployees = array_values(array_filter($uniqueEmployees, function ($e) use ($allowedIds) {
                    return in_array($e['id'], $allowedIds);
                }));
            }
            if(sizeof($uniqueEmployees)>0){
                foreach ($uniqueEmployees as $employee) {
                    $employees[$employee['lineManagerId']][] = $employee;
                }
            }

            $employeeIds = array_column($uniqueEmployees, 'id');

            $startDate = date('Y-m-d', strtotime($filterBy['year'] . '-' . $filterBy['startMonth'] . '-01')) . ' 00:00:00';
            $endDate   = date('Y-m-t', strtotime($filterBy['year'] . '-' . $filterBy['endMonth'] . '-01')) . ' 23:59:59';
            $records = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->getVisitDetailsSummery($startDate, $endDate, $employeeIds);
        }
        $processes = ['agent', 'sub-agent', 'other-agent', 'farmer'];

        return $this->render("@TerminalbdCrm/report/visit-status/visit-summery-report.html.twig",[
            'records' => $records,
            'form' => $form->createView(),
            'selectedEmployee' => $selectedEmployee,
            'filterBy' => $filterBy,
            'months' => $months,
            'employees' => $employees,
            'processes' => $processes,
        ]);
    }

    private function unique_array($my_array, $key) {
        $result = array();   // Initialize an empty array to store the unique values
        $i = 0;              // Initialize a counter
        $key_array = array(); // Initialize an array to keep track of encountered keys

        // Iterate through each element in the input array
        foreach($my_array as $val) {
            // Check if the key value is not already present in the key array
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];  // Store the key value in the key array
                $result[$i] = $val;           // Store the entire element in the result array
            }
            $i++;  // Increment the counter
        }

        // Return the array containing unique values based on the specified key
        return $result;
    }

    private function monthRange($start, $end)
    {

        $current = $start;
        $data = [];
        while ($current <= $end) {

//            $next = @date('Y-M-01', $current) . "+1 month";
            $next = @date('Y-M-01', $current);
            $current = @strtotime($next);

            $data[] = date('F', $current);

            $next = @date('Y-M-01', $current) . "+1 month";
            $current = @strtotime($next);
        }
        return $data;
    }
}