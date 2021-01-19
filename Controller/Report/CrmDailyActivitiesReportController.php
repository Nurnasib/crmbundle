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
 * @Route("/crm/report")
 */
class CrmDailyActivitiesReportController extends AbstractController
{
    /**
     * @Route("/daily-activities", name="daily_activities")
     */
    public function dailyReport(Request $request){
        $form = $this->createFormBuilder()
            ->add('date', TextType::class,[
                'attr' =>[
                    'autocomplete' => 'off'
                ]
            ])
//            ->add('Region', EntityType::class,[
//                'class' => Location::class
//            ])
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
//            dd($activities);

            if (empty($activities)){
//                dd($activities);
                $this->addFlash('message', "No Activities are found for " . $date['date'] );
                return $this->redirectToRoute('daily_activities');
            }else{
                return $this->render("@TerminalbdCrm/report/crmDailyActivities/daily-activities-report.html.twig", ['data'=>$activities]);
            }
        }
        return $this->render("@TerminalbdCrm/report/crmDailyActivities/index.html.twig", ['form'=>$form->createView()]);
    }

    public function crmDailyReport($date)
    {
        $filterBy = [
            'employeeId' => $this->getUser()->getId(),
            'startDate' => $date['date'] . ' 00:00:00',
            'endDate' => $date['date'] . ' 23:59:59'
        ];


        $data = $this->getDoctrine()->getRepository(CrmVisit::class)->findDailyReport($filterBy);

//        if (empty($data)){
//            $data = 'No Data Found!';
//            $this->addFlash('message', "No Activities are found for $date");
//            $this->redirectToRoute('daily_activities');
//        }
//        dd($data);
        return $data;
    }
}