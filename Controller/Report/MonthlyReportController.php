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
        }
        return $this->render('@TerminalbdCrm/report/monthlyReport/index.html.twig', ['form' => $form->createView(),
            'entities' => $entities,
            'filterBy' => $filterBy,
            'lifeCycleSlug' => $report ? $report->getSlug() : null,
            'employee' => $employee,
            'report' => $report,
            'species' => $species,]);
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
                $sheet->mergeCells("D1:AD1")->getStyle("D1:U1")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("D1", "NOURISH POULTRY AND HATCHERY LTD");
                $sheet->mergeCells("D2:AD2")->getStyle("D2:U2")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("D2", "UTTARA, DHAKA.");
                $sheet->mergeCells("D3:AD3")->getStyle("D3:U3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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

            case 'fcr-after-sale-boiler':
                //Header Left
                $sheet->getStyle("A1:D7")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->setCellValue("A1", "No of Farms");

                $totalRecord = 0;
                $lessThanFifty = 0;
                $moreThanFifty = 0;

                if (isset($data['totalRecord'])){
                    $totalRecord = $data['totalRecord'];
                }
                $sheet->setCellValue("B1", $totalRecord);
                $sheet->setCellValue("C1", "%");
                $sheet->setCellValue("D1", "Comments");

                $sheet->setCellValue("A2", "FCR Below of <=1.45");

                $excellent = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['excellent'])){
                    $excellent = count($data['fcrWithMortalitySummery']['excellent']);
                    $lessThanFifty += $excellent;
                }

                $excellentPercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['excellent'])){
                    $excellentPercentage = round(((count($data['fcrWithMortalitySummery']['excellent']) * 100) / $data['totalRecord']), 2);
                }

                $sheet->setCellValue("B2", $excellent);
                $sheet->setCellValue("C2", $excellentPercentage);
                $sheet->setCellValue("D2", "Excellent");

                $veryGood = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['very_good'])){
                    $veryGood = count($data['fcrWithMortalitySummery']['very_good']);
                    $lessThanFifty += $veryGood;

                }

                $veryGoodPercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['very_good'])){
                    $veryGoodPercentage = round(((count($data['fcrWithMortalitySummery']['very_good']) * 100) / $data['totalRecord']), 2);
                }
                $sheet->setCellValue("A3", "FCR Between (1.46-1.49)");
                $sheet->setCellValue("B3", $veryGood);
                $sheet->setCellValue("C3", $veryGoodPercentage);
                $sheet->setCellValue("D3", "Very Good");

                $good = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['good'])){
                    $good = count($data['fcrWithMortalitySummery']['good']);
                    $moreThanFifty += $good;
                }
                $goodPercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['good'])){
                    $goodPercentage = round(((count($data['fcrWithMortalitySummery']['good']) * 100) / $data['totalRecord']),2);
                }
                $sheet->setCellValue("A4", "FCR Between (1.50-1.52)");
                $sheet->setCellValue("B4", $good);
                $sheet->setCellValue("C4", $goodPercentage);
                $sheet->setCellValue("D4", "Good");

                $moderate = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['moderate'])){
                    $moderate = count($data['fcrWithMortalitySummery']['moderate']);
                    $moreThanFifty += $moderate;

                }
                $moderatePercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['moderate'])){
                    $moderatePercentage = round(((count($data['fcrWithMortalitySummery']['moderate']) * 100) / $data['totalRecord']), 2);
                }

                $sheet->setCellValue("A5", "FCR Between (1.53-1.55)");
                $sheet->setCellValue("B5", $moderate);
                $sheet->setCellValue("C5", $moderatePercentage);
                $sheet->setCellValue("D5", "Moderate");

                $bad = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['bad'])){
                    $bad = count($data['fcrWithMortalitySummery']['bad']);
                    $moreThanFifty += $bad;

                }
                $badPercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['bad'])){
                    $badPercentage = round(((count($data['fcrWithMortalitySummery']['bad']) * 100) / $data['totalRecord']), 2);
                }
                $sheet->setCellValue("A6", "FCR Between (1.56-1.60)");
                $sheet->setCellValue("B6", $bad);
                $sheet->setCellValue("C6", $badPercentage);
                $sheet->setCellValue("D6", "Bad");

                $veryBad = 0;
                if (isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['very_bad'])){
                    $veryBad = count($data['fcrWithMortalitySummery']['very_bad']);
                    $moreThanFifty += $veryBad;

                }
                $veryBadPercentage = 0;
                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery']) && isset($data['fcrWithMortalitySummery']['very_bad'])){
                    $veryBadPercentage = round(((count($data['fcrWithMortalitySummery']['very_bad']) * 100) / $data['totalRecord']), 2);
                }
                $sheet->setCellValue("A7", "FCR Above of >1.60");
                $sheet->setCellValue("B7", $veryBad);
                $sheet->setCellValue("C7", $veryBadPercentage);
                $sheet->setCellValue("D7", "Very Bad");
                //Header Left END


                //Header Center
                $sheet->mergeCells("E1:Z1")->getStyle("E1:Z1")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("E1", "NOURISH POULTRY AND HATCHERY LTD");
                $sheet->mergeCells("E2:Z2")->getStyle("E2:Z2")->applyFromArray($headerStyleArray);
                $sheet->setCellValue("E2", "UTTARA, DHAKA.");
                $sheet->mergeCells("E3:Z3")->getStyle("E3:Z3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("E3", $report->getName() . ' ' . $filterBy['startDate'] . ' to ' . $filterBy['endDate']);
                //Header Center END

                //Header Right
                $sheet->getStyle("AA1:AD3")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->setCellValue("AA1", "No of Farms");
                $sheet->setCellValue("AB1", $totalRecord);
                $sheet->setCellValue("AC1", "%");
                $sheet->setCellValue("AD1", "Comments");


                $lessThanFiftyPercentage = 0;
                $moreThanFiftyPercentage = 0;

                if (isset($data['totalRecord']) && $data['totalRecord'] > 0 && isset($data['fcrWithMortalitySummery'])){
                    $lessThanFiftyPercentage = round((($lessThanFifty * 100) / $data['totalRecord']), 2);
                    $moreThanFiftyPercentage = round((($moreThanFifty * 100) / $data['totalRecord']), 2);

                }
                $sheet->setCellValue("AA2", "FCR Below of <1.50");
                $sheet->setCellValue("AB2", $lessThanFifty);
                $sheet->setCellValue("AC2", $lessThanFiftyPercentage);
                $sheet->setCellValue("AD2", "Good");

                $sheet->setCellValue("AA3", "FCR Above of ≥1.50");
                $sheet->setCellValue("AB3", $moreThanFifty);
                $sheet->setCellValue("AC3", $moreThanFiftyPercentage);
                $sheet->setCellValue("AD3", "Bad");
                //Header Right END


                // Data header
                $sheet->mergeCells("J8:K8")->getStyle("J8:K8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("J8", "Mortality")->getStyle("J8")->getFont()->setBold(true);

                $sheet->mergeCells("L8:M8")->getStyle("L8:M8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("L8", "Weight (gm)")->getStyle("L8")->getFont()->setBold(true);

                $sheet->mergeCells("N8:P8")->getStyle("N8:P8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("N8", "Feed Consumption")->getStyle("N8")->getFont()->setBold(true);

                $sheet->mergeCells("Q8:S8")->getStyle("Q8:S8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("Q8", "FCR")->getStyle("Q8")->getFont()->setBold(true);

                $sheet->mergeCells("W8:Y8")->getStyle("W8:Y8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("W8", "CSO Wise")->getStyle("W8")->getFont()->setBold(true);

                $sheet->mergeCells("Z8:AB8")->getStyle("Z8:AB8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("Z8", "Region Wise")->getStyle("Z8")->getFont()->setBold(true);

                $sheet->setCellValue("A9", "Month")->getStyle("A9")->getFont()->setBold(true);
                $sheet->setCellValue("B9", "Region")->getStyle("B9")->getFont()->setBold(true);
                $sheet->setCellValue("C9", "Name Of CSO")->getStyle("C9")->getFont()->setBold(true);
                $sheet->setCellValue("D9", "Agent")->getStyle("D9")->getFont()->setBold(true);
                $sheet->setCellValue("E9", "District")->getStyle("E9")->getFont()->setBold(true);
                $sheet->setCellValue("F9", "Address")->getStyle("F9")->getFont()->setBold(true);
                $sheet->setCellValue("G9", "Hatching date")->getStyle("G9")->getFont()->setBold(true);
                $sheet->setCellValue("H9", "Total Birds")->getStyle("H9")->getFont()->setBold(true);
                $sheet->setCellValue("I9", "Age (Day)")->getStyle("I9")->getFont()->setBold(true);
                $sheet->setCellValue("J9", "Pcs")->getStyle("J9")->getFont()->setBold(true);
                $sheet->setCellValue("K9", "%")->getStyle("K9")->getFont()->setBold(true);
                $sheet->setCellValue("L9", "Actual")->getStyle("L9")->getFont()->setBold(true);
                $sheet->setCellValue("M9", "Standard")->getStyle("M9")->getFont()->setBold(true);
                $sheet->setCellValue("N9", "Total (kg)")->getStyle("N9")->getFont()->setBold(true);
                $sheet->setCellValue("O9", "Per bird (gm)")->getStyle("O9")->getFont()->setBold(true);
                $sheet->setCellValue("P9", "Standard (gm)")->getStyle("P9")->getFont()->setBold(true);
                $sheet->setCellValue("Q9", "Without M")->getStyle("Q9")->getFont()->setBold(true);
                $sheet->setCellValue("R9", "With M")->getStyle("R9")->getFont()->setBold(true);
                $sheet->setCellValue("S9", "Standard")->getStyle("S9")->getFont()->setBold(true);
                $sheet->setCellValue("T9", "cFCR (2000 gm Std)")->getStyle("T9")->getFont()->setBold(true);
                $sheet->setCellValue("U9", "Hatchery")->getStyle("U9")->getFont()->setBold(true);
                $sheet->setCellValue("V9", "Breed")->getStyle("V9")->getFont()->setBold(true);
                $sheet->setCellValue("W9", "Avg. FCR")->getStyle("W9")->getFont()->setBold(true);
                $sheet->setCellValue("X9", "cFCR (2000 gm Std)")->getStyle("X9")->getFont()->setBold(true);
                $sheet->setCellValue("Y9", "Avg. Age")->getStyle("Y9")->getFont()->setBold(true);
                $sheet->setCellValue("Z9", "Avg. FCR")->getStyle("Z9")->getFont()->setBold(true);
                $sheet->setCellValue("AA9", "cFCR (2000 gm Std)")->getStyle("AA9")->getFont()->setBold(true);
                $sheet->setCellValue("AB9", "Avg. Age")->getStyle("AB9")->getFont()->setBold(true);
                $sheet->setCellValue("AC9", "Feed")->getStyle("AC9")->getFont()->setBold(true);
                $sheet->setCellValue("AD9", "FeedMill")->getStyle("AD9")->getFont()->setBold(true);
                $sheet->setCellValue("AE9", "Remarks")->getStyle("AE9")->getFont()->setBold(true);
                // Data header END


                $dataCellCoordinate = 10;
                foreach ($data as $monthYear => $regionFcrDetails) {

                    if (
                        $monthYear !== 'totalRecord' &&
                        $monthYear !== 'fcrBoilerStandard' &&
                        $monthYear !== 'fcrWithMortalitySummery'
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
                                            $sheet->setCellValue("F" . $dataCellCoordinate, $fcrDetail['agentAddress']);
                                            $sheet->setCellValue("G" . $dataCellCoordinate, $fcrDetail['hatchingDate']->format('d-m-Y'));
                                            $sheet->setCellValue("H" . $dataCellCoordinate, $fcrDetail['totalBirds']);
                                            $sheet->setCellValue("I" . $dataCellCoordinate, $fcrDetail['ageDay']);
                                            $sheet->setCellValue("J" . $dataCellCoordinate, $fcrDetail['mortalityPes']);
                                            $sheet->setCellValue("K" . $dataCellCoordinate, round($fcrDetail['mortalityPercent'],2));
                                            $sheet->setCellValue("L" . $dataCellCoordinate, round($fcrDetail['weight'],2));
                                            $sheet->setCellValue("M" . $dataCellCoordinate, $fcrDetail['weightStandard']);
                                            $sheet->setCellValue("N" . $dataCellCoordinate, round($fcrDetail['feedConsumptionTotalKg'], 2));
                                            $sheet->setCellValue("O" . $dataCellCoordinate, round($fcrDetail['feedConsumptionPerBird'], 2));
                                            $sheet->setCellValue("P" . $dataCellCoordinate, $fcrDetail['feedConsumptionStandard']);
                                            $sheet->setCellValue("Q" . $dataCellCoordinate, round($fcrDetail['fcrWithoutMortality'], 2));
                                            $sheet->setCellValue("R" . $dataCellCoordinate, round($fcrDetail['fcrWithMortality'], 2));

                                            $standard = 0;
                                            if (isset($data['fcrBoilerStandard']) && isset($data['fcrBoilerStandard'][$fcrDetail['ageDay']]) && $data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetBodyWeight'] > 0){
                                                $targetConsumption = $data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetFeedConsumption'];
                                                $targetBodyWeight = $data['fcrBoilerStandard'][$fcrDetail['ageDay']]['targetBodyWeight'];
                                                $standard = $targetConsumption / $targetBodyWeight;
                                            }
                                            $sheet->setCellValue("S" . $dataCellCoordinate, round($standard, 3));
                                            $sheet->setCellValue("T" . $dataCellCoordinate, $fcrDetail['cfcr']);
                                            $sheet->setCellValue("U" . $dataCellCoordinate, $fcrDetail['hatcheryName']);
                                            $sheet->setCellValue("V" . $dataCellCoordinate, $fcrDetail['breedBame']);

                                            if ($counter === 1) {
                                                $sheet->mergeCells("W" . $dataCellCoordinate . ":W" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getStyle("W" . $dataCellCoordinate . ":W" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("W" . $dataCellCoordinate, (count($employeesFcrDetails[$employeeId]['details']) > 0 ? round((array_sum(array_column($employeesFcrDetails[$employeeId]['details'], 'fcrWithMortality'))/count($employeesFcrDetails[$employeeId]['details'])),3) : ''));

                                                $sheet->mergeCells("X" . $dataCellCoordinate . ":X" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getStyle("X" . $dataCellCoordinate . ":X" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("X" . $dataCellCoordinate, (count($employeesFcrDetails[$employeeId]['details']) > 0 ? round((array_sum(array_column($employeesFcrDetails[$employeeId]['details'], 'cfcr')) / count($employeesFcrDetails[$employeeId]['details'])), 3) : ''));

                                                $sheet->mergeCells("Y" . $dataCellCoordinate . ":Y" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getStyle("Y" . $dataCellCoordinate . ":Y" . (($dataCellCoordinate + count($fcrDetails['details'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("Y" . $dataCellCoordinate, (count($employeesFcrDetails[$employeeId]['details']) > 0 ? round((array_sum(array_column($employeesFcrDetails[$employeeId]['details'],'ageDay'))/count($employeesFcrDetails[$employeeId]['details'])),3) : ''));
                                            }

                                            if ($recordCount === 1) {
                                                $sheet->mergeCells("Z" . $dataCellCoordinate . ":Z" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getStyle("Z" . $dataCellCoordinate . ":Z" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("Z" . $dataCellCoordinate, (count($regionFcrDetails[$regionId]['recordCount']) > 0 ? round((array_sum(array_column($regionFcrDetails[$regionId]['recordCount'],'fcrWithMortality')) / count($regionFcrDetails[$regionId]['recordCount'])),3) : ''));

                                                $sheet->mergeCells("AA" . $dataCellCoordinate . ":AA" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getStyle("AA" . $dataCellCoordinate . ":AA" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("AA" . $dataCellCoordinate, (count($regionFcrDetails[$regionId]['recordCount']) > 0 ? round((array_sum(array_column($regionFcrDetails[$regionId]['recordCount'],'cfcr')) / count($regionFcrDetails[$regionId]['recordCount'])),3) :''));

                                                $sheet->mergeCells("AB" . $dataCellCoordinate . ":AB" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getStyle("AB" . $dataCellCoordinate . ":AB" . (($dataCellCoordinate + count($employeesFcrDetails['recordCount'])) - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                $sheet->setCellValue("AB" . $dataCellCoordinate, (count($regionFcrDetails[$regionId]['recordCount']) > 0 ? round((array_sum(array_column($regionFcrDetails[$regionId]['recordCount'],'ageDay')) / count($regionFcrDetails[$regionId]['recordCount'])),3) : '' ));
                                            }


                                            $sheet->setCellValue("AC" . $dataCellCoordinate, $fcrDetail['feedName']);
                                            $sheet->setCellValue("AD" . $dataCellCoordinate, $fcrDetail['feedMillName']);
                                            $sheet->setCellValue("AE" . $dataCellCoordinate, $fcrDetail['remarks']);

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