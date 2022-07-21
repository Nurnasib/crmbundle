<?php


namespace Terminalbd\CrmBundle\Controller\Report;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
use Terminalbd\CrmBundle\Entity\CattlePerformanceDetails;
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcrDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;

/**
 * Class MonthlyReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_CRM_REPORT') or is_granted('ROLE_DEVELOPER')")
 * @Route("/crm/report/monthly", name="")
 */
class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/", name="monthly_report_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ParameterBagInterface $parameterBag)
    {
        $filterBy = [];
        $entities = [];
        $species = [];
        $employee = null;
        $report = null;
        $form = $this->createForm(SearchFilterFormType::class, null, ['loggedUser' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isExcel = $request->request->get('excel');
            $report = $form->getData()['monthlyReport'];

            $filterBy['startDate'] = $form->getData()['startDate'];
            $filterBy['endDate'] = $form->getData()['endDate'];
            $filterBy['employeeId'] = $form->getData()['employee'] ? $form->getData()['employee']->getId() : '';
            $filterBy['feedMill'] = $form->getData()['feedMill'] ? $form->getData()['feedMill']->getId() : '';
            $filterBy['region'] = $form->getData()['region'] ? $form->getData()['region']->getId() : '';

            $employee = $form->getData()['employee'];

            switch ($report->getSlug()) {
                case 'fcr-before-sale-boiler':
                case 'fcr-after-sale-boiler':
                case 'fcr-before-sale-sonali':
                case 'fcr-after-sale-sonali':
                    $entities = $this->getDoctrine()->getRepository(FcrDetails::class)->getFcrDetailsByEmployee($report, $filterBy);
                    break;
                case 'layer-performance-brown':
                case 'layer-performance-white':
                    $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByEmployeeAndDate($report, $filterBy);
                    break;
                case 'antibiotic-free-farm-poultry':
                    $entities = $this->getDoctrine()->getRepository(AntibioticFreeFarm::class)->getAntibioticFreeFarmByEmployeeAndDate($report, $filterBy);
                    break;
                case 'less-costing-farm-poultry':
                case 'less-costing-farm-fish':
                    $entities = $this->getDoctrine()->getRepository(CostBenefitAnalysisForLessCostingFarm::class)->getLessCostingFarmByEmployeeAndDate($report, $filterBy);
                    break;
                case 'disease-mapping-report-poultry':
                case 'disease-mapping-report-cattle':
                case 'disease-mapping-report-fish':
                    $entities = $this->getDoctrine()->getRepository(DiseaseMapping::class)->getDiseasesMappingReportByEmployeeDate($report, $filterBy, $this->getUser());
                    break;
                case 'farmer-introduce-report-poultry':
                case 'farmer-introduce-report-fish':
                case 'farmer-introduce-report-cattle':
                    $entities = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->getFarmerIntroduceReportByEmployeeDate($report, $filterBy);
                    break;

                case 'company-species-wise-average-fcr-before':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()) {
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('BEFORE', $filterBy, $this->getUser());

                    break;

                case 'company-species-wise-average-fcr-after':
                    $speciesObj = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'SPECIES_NAME', 'status' => true]);
                    foreach ($speciesObj as $item) {
                        if ($item->getParent()) {
                            $species[$item->getParent()->getName()][] = $item;
                        }
                    }
                    $entities = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcrDetails::class)->getAverageFcrReport('AFTER', $filterBy, $this->getUser());

                    break;

                case 'fattening-performance-report':
                case 'dairy-performance-report':
                    $entities = $this->getDoctrine()->getRepository(CattlePerformanceDetails::class)->getPerformanceReport($report, $filterBy, $this->getUser());

                    break;

                case 'cattle-farm-visit-report':
                    $entities = $this->getDoctrine()->getRepository(CattleFarmVisitDetails::class)->getCattleFarmVisitReport($report, $filterBy, $this->getUser());
                    break;

                default:
                    $entities = [];
                    break;
            }

            if ($isExcel && $entities) {
                return $this->downloadExcel($report, $filterBy, $parameterBag, $entities);
            }

            return $this->render('@TerminalbdCrm/report/monthlyReport/index.html.twig', ['form' => $form->createView(),
                'entities' => $entities,
                'filterBy' => $filterBy,
                'lifeCycleSlug' => $report ? $report->getSlug() : null,
                'employee' => $employee,
                'report' => $report,
                'species' => $species,]);

        }
    }

    private function downloadExcel($report, $filterBy, $parameterBag, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->setTitle(str_replace('-', ' ', $report->getSlug())); //Sheet name

        switch ($report->getSlug()) {
            case 'fcr-before-sale-boiler':
                //Header Left
                $sheet->getStyle("A1:C3")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->setCellValue("A1", "No of Farms");
                $sheet->setCellValue("B1", $data['totalRecord'] ? $data['totalRecord'] : '');
                $sheet->setCellValue("C1", "%");

                $sheet->setCellValue("A2", "Body Wt > Stad.");
                $sheet->setCellValue("B2", $data['bodyWtGatterThanStandard'] ? $data['bodyWtGatterThanStandard'] : '');
                $sheet->setCellValue("C2", ($data['bodyWtGatterThanStandard'] && $data['totalRecord'] && $data['totalRecord'] > 0) ? (($data['bodyWtGatterThanStandard'] * 100) / $data['totalRecord']) | number_format(0) : '');

                $sheet->setCellValue("A3", "Body Wt < Stad.");
                $sheet->setCellValue("B3", $data['bodyWtLessThanStandard'] ? $data['bodyWtLessThanStandard'] : '');
                $sheet->setCellValue("C3", ($data['bodyWtLessThanStandard'] && $data['totalRecord'] && $data['totalRecord'] > 0) ? (($data['bodyWtLessThanStandard'] * 100) / $data['totalRecord']) | number_format(0) : '');
                //Header Left END


                //Header Right
                $sheet->mergeCells("D1:AC1")->getStyle("D1:U1")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("D1", "NOURISH POULTRY AND HATCHERY LTD");
                $sheet->mergeCells("D2:AC2")->getStyle("D2:U2")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("D2", "UTTARA, DHAKA.");
                $sheet->mergeCells("D3:AC3")->getStyle("D3:U3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("D3", $report->getName() . ' ' . $filterBy['startDate'] . ' to ' . $filterBy['endDate']);
                //Header Right END


                // Data header
                $sheet->mergeCells("K5:L5")->getStyle("K5:L5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("K5", "Mortality")->getStyle("K5")->getFont()->setBold(true);

                $sheet->mergeCells("M5:O5")->getStyle("M5:O5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("M5", "Weight (gm)")->getStyle("M5")->getFont()->setBold(true);

                $sheet->mergeCells("P5:R5")->getStyle("P5:R5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("P5", "Feed Consumption")->getStyle("P5")->getFont()->setBold(true);

                $sheet->mergeCells("S5:U5")->getStyle("S5:U5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("S5", "FCR")->getStyle("S5")->getFont()->setBold(true);

                $sheet->setCellValue("A6", "Month")->getStyle("A6")->getFont()->setBold(true);
                $sheet->setCellValue("B6", "Region")->getStyle("B6")->getFont()->setBold(true);
                $sheet->setCellValue("C6", "Name Of CSO")->getStyle("C6")->getFont()->setBold(true);
                $sheet->setCellValue("D6", "Name Of Agent")->getStyle("D6")->getFont()->setBold(true);
                $sheet->setCellValue("E6", "District")->getStyle("E6")->getFont()->setBold(true);
                $sheet->setCellValue("F6", "Name Of Farmer")->getStyle("F6")->getFont()->setBold(true);
                $sheet->setCellValue("G6", "Address")->getStyle("G6")->getFont()->setBold(true);
                $sheet->setCellValue("H6", "Mobile")->getStyle("H6")->getFont()->setBold(true);
                $sheet->setCellValue("I6", "Hatching date")->getStyle("I6")->getFont()->setBold(true);
                $sheet->setCellValue("J6", "Total Birds")->getStyle("J6")->getFont()->setBold(true);
                $sheet->setCellValue("K6", "Age (Day)")->getStyle("K6")->getFont()->setBold(true);
                $sheet->setCellValue("L6", "Pcs")->getStyle("L6")->getFont()->setBold(true);
                $sheet->setCellValue("M6", "%")->getStyle("M6")->getFont()->setBold(true);
                $sheet->setCellValue("N6", "Achieve")->getStyle("N6")->getFont()->setBold(true);
                $sheet->setCellValue("O6", "Standard")->getStyle("O6")->getFont()->setBold(true);
                $sheet->setCellValue("P6", "Difference")->getStyle("P6")->getFont()->setBold(true);
                $sheet->setCellValue("Q6", "Total (kg)")->getStyle("Q6")->getFont()->setBold(true);
                $sheet->setCellValue("R6", "Per bird (gm)")->getStyle("R6")->getFont()->setBold(true);
                $sheet->setCellValue("S6", "Standard")->getStyle("S6")->getFont()->setBold(true);
                $sheet->setCellValue("T6", "Without M")->getStyle("T6")->getFont()->setBold(true);
                $sheet->setCellValue("U6", "With M")->getStyle("U6")->getFont()->setBold(true);
                $sheet->setCellValue("V6", "Standard")->getStyle("V6")->getFont()->setBold(true);
                $sheet->setCellValue("W6", "Hatchery")->getStyle("W6")->getFont()->setBold(true);
                $sheet->setCellValue("X6", "Breed")->getStyle("X6")->getFont()->setBold(true);
                $sheet->setCellValue("Y6", "Feed")->getStyle("Y6")->getFont()->setBold(true);
                $sheet->setCellValue("Z6", "FeedMill")->getStyle("Z6")->getFont()->setBold(true);
                $sheet->setCellValue("AA6", "FeedType")->getStyle("AA6")->getFont()->setBold(true);
                $sheet->setCellValue("AB6", "Pro. Date")->getStyle("AB6")->getFont()->setBold(true);
                $sheet->setCellValue("AC6", "Batch No.")->getStyle("AC6")->getFont()->setBold(true);
                $sheet->setCellValue("AD6", "Remarks")->getStyle("AD6")->getFont()->setBold(true);
                // Data header END


                $dataCellCoordinate = 7;
                foreach ($data as $monthYear => $regionFcrDetails) {

                    if (
                        $monthYear !== 'totalRecord' &&
                        $monthYear !== 'fcrBoilerStandard' &&
                        $monthYear !== 'bodyWtGatterThanStandard' &&
                        $monthYear !== 'bodyWtLessThanStandard'
                    ) {
                        $count = 1;

                        foreach ($regionFcrDetails as $regionId => $employeesFcrDetails) {

                            if ($regionId != 'monthRecordCount') {
                                $recordCount = 1;

                                foreach ($employeesFcrDetails as $employeeId => $fcrDetails) {

                                    if ($employeeId !== 'recordCount') {
                                        $counter = 1;

                                        foreach ($fcrDetails['details'] as $fcrDetail) {

                                            if ($count === 1) {
                                                $sheet->mergeCells("A" . $dataCellCoordinate . ":A" . (($dataCellCoordinate + count($regionFcrDetails['monthRecordCount'])) - 1))->getStyle("A" . $dataCellCoordinate . ":A" . (($dataCellCoordinate + count($regionFcrDetails['monthRecordCount'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                                                $sheet->setCellValue("A" . $dataCellCoordinate, $monthYear);
                                            }

                                            if ($recordCount === 1) {
                                                $sheet->mergeCells("B" . $dataCellCoordinate . ":B" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getStyle("B" . $dataCellCoordinate . ":B" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("B" . $dataCellCoordinate, $fcrDetail['regionName']);
                                            }

                                            if ($counter === 1) {
                                                $sheet->mergeCells("C" . $dataCellCoordinate . ":C" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getStyle("C" . $dataCellCoordinate . ":C" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("C" . $dataCellCoordinate, $fcrDetail['employeeName']);
                                            }

                                            $sheet->setCellValue("D" . $dataCellCoordinate, $fcrDetail['agentName']);
                                            $sheet->setCellValue("E" . $dataCellCoordinate, $fcrDetail['agentDistrictName']);
                                            $sheet->setCellValue("F" . $dataCellCoordinate, $fcrDetail['customerName']);
                                            $sheet->setCellValue("G" . $dataCellCoordinate, $fcrDetail['customerAddress']);
                                            $sheet->setCellValue("H" . $dataCellCoordinate, $fcrDetail['customerMobile']);
                                            $sheet->setCellValue("I" . $dataCellCoordinate, $fcrDetail['hatchingDate']->format('d-m-Y'));
                                            $sheet->setCellValue("J" . $dataCellCoordinate, $fcrDetail['totalBirds']);
                                            $sheet->setCellValue("K" . $dataCellCoordinate, $fcrDetail['ageDay']);
                                            $sheet->setCellValue("L" . $dataCellCoordinate, $fcrDetail['mortalityPes']);
                                            $sheet->setCellValue("M" . $dataCellCoordinate, round($fcrDetail['mortalityPercent'], 2));
                                            $sheet->setCellValue("N" . $dataCellCoordinate, round($fcrDetail['weight'], 2));
                                            $sheet->setCellValue("O" . $dataCellCoordinate, $fcrDetail['weightStandard']);
                                            $sheet->setCellValue("P" . $dataCellCoordinate, round(($fcrDetail['weight'] - $fcrDetail['weightStandard']), 2));
                                            $sheet->setCellValue("Q" . $dataCellCoordinate, round($fcrDetail['feedConsumptionTotalKg'], 2));
                                            $sheet->setCellValue("R" . $dataCellCoordinate, round($fcrDetail['feedConsumptionPerBird'], 2));
                                            $sheet->setCellValue("S" . $dataCellCoordinate, $fcrDetail['feedConsumptionStandard']);
                                            $sheet->setCellValue("T" . $dataCellCoordinate, round($fcrDetail['fcrWithoutMortality'], 2));
                                            $sheet->setCellValue("U" . $dataCellCoordinate, round($fcrDetail['fcrWithMortality'], 2));

                                            if (
                                                isset($data['fcrBoilerStandard']) &&
                                                isset($data['fcrBoilerStandard'][$fcrDetail['ageDay']]) &&
                                                $data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetBodyWeight'] > 0
                                            ) {
                                                $value = round($data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetFeedConsumption'] / $data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetBodyWeight'], 3);

                                                $sheet->setCellValue("V" . $dataCellCoordinate, $value);
                                            } else {
                                                $sheet->setCellValue("V" . $dataCellCoordinate, "");
                                            }

                                            $sheet->setCellValue("W" . $dataCellCoordinate, $fcrDetail['hatcheryName']);
                                            $sheet->setCellValue("X" . $dataCellCoordinate, $fcrDetail['breedBame']);
                                            $sheet->setCellValue("Y" . $dataCellCoordinate, $fcrDetail['feedName']);
                                            $sheet->setCellValue("Z" . $dataCellCoordinate, $fcrDetail['feedMillName']);
                                            $sheet->setCellValue("AA" . $dataCellCoordinate, $fcrDetail['feedTypeName']);
                                            $sheet->setCellValue("AB" . $dataCellCoordinate, $fcrDetail['proDate']->format('d-m-Y'));
                                            $sheet->setCellValue("AC" . $dataCellCoordinate, $fcrDetail['batchNo']);
                                            $sheet->setCellValue("AD" . $dataCellCoordinate, $fcrDetail['remarks']);

                                            $counter++;
                                            $recordCount++;
                                            $dataCellCoordinate++;
                                            $count++;
                                        }
                                    }
                                }
                            }

                        }
                    }

                }
                break;
        }

        for ($col = 'A'; $col != $sheet->getHighestColumn(); $col++) { //extend Column Width
            $sheet->getColumnDimension("$col")->setAutoSize(true);
        }

        // Create xlsx file
//            $filePath = $parameterBag->get('projectRoot') . '/public/uploads/problem_agent_sales_'.$month . '_' . $year . '_' . date('d-m-Y_H-s-i') .'_.xlsx';
        $filePath = $parameterBag->get('projectRoot') . '/public/uploads/' . $report->getSlug() . '-' . date('d-m-Y_H-s-i') . '_.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setIncludeCharts(true);
        $writer->save($filePath);
        return $this->file($filePath)->deleteFileAfterSend();
    }
}