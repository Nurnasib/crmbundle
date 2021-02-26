<?php
namespace Terminalbd\CrmBundle\Controller\Report;

use App\Entity\Admin\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\CrmVisit;

/**
 * Class CrmDailyActivitiesReportController
 * @package Terminalbd\CrmBundle\Controller\Report
 */
class CrmDailyActivitiesReportController extends AbstractController
{
    /**
     * @Route("/crm/daily-activities/report", name="daily_activities_report")
     */
    public function dailyReport(Request $request)
    {
        $activities = [];
        $form = $this->createFormBuilder()
            ->add('date', TextType::class,[
                'attr' =>[
                    'autocomplete' => 'off',
                    'value' => '2021-01-14'
                ]
            ])
            ->add('FindReport', SubmitType::class,[
                'attr'=>[
                    'class' =>'btn btn-primary btn-sm'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $date = $form->getData();
            $activities = $this->crmDailyReport($date);
//            dd($date);

            if (empty($activities)){
//                dd($activities);
                $this->addFlash('message', "No Activities are found for " . $date['date'] );
                return $this->redirectToRoute('daily_activities_report');
            }else{
                return $this->render("@TerminalbdCrm/report/customerDailyActivities/report-daily-activities.html.twig", ['form'=>$form->createView(),'data'=>$activities]);
            }
        }
        return $this->render("@TerminalbdCrm/report/customerDailyActivities/report-daily-activities.html.twig", ['form'=>$form->createView()]);
    }

    public function crmDailyReport($date)
    {
        $filterBy = [
            'employeeId' => $this->getUser()->getId(),
            'startDate' => $date['date'] . ' 00:00:00',
            'endDate' => $date['date'] . ' 23:59:59'
        ];

//        dd($filterBy);
        $data = $this->getDoctrine()->getRepository(CrmVisit::class)->findDailyReport($filterBy);
        return $data;
    }
}