<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Form;


use App\Entity\Admin\Location;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Fcr;
use App\Entity\Core\Setting;
use function Doctrine\ORM\QueryBuilder;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class SearchFilterFormTypeForVisitSummeryReport extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['loggedUser'];
        $userRepo = $options['userRepo'];
        $builder

            ->add('startMonth', ChoiceType::class, [
                'choices' => [
                    'January' => 'January',
                    'February' => 'February',
                    'March' => 'March',
                    'April' => 'April',
                    'May' => 'May',
                    'June' => 'June',
                    'July' => 'July',
                    'August' => 'August',
                    'September' => 'September',
                    'October' => 'October',
                    'November' => 'November',
                    'December' => 'December',
                ],
                'required' => false,
                'placeholder' => 'Select a Month',
                'data' => date('F'),

            ])
            ->add('endMonth', ChoiceType::class, [
                'choices' => [
                    'January' => 'January',
                    'February' => 'February',
                    'March' => 'March',
                    'April' => 'April',
                    'May' => 'May',
                    'June' => 'June',
                    'July' => 'July',
                    'August' => 'August',
                    'September' => 'September',
                    'October' => 'October',
                    'November' => 'November',
                    'December' => 'December',
                ],
                'required' => false,
                'placeholder' => 'Select a Month',
                'data' => date('F'),
            ])
            ->add('year', ChoiceType::class, [
                'choices' => $this->getYears(2020),
                'required' => false,
                'placeholder' => 'Select Year',
                'data' => date('Y'),
            ])
            ->add('employee', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($user){
                    return '(' . $user->getUserId() . ') ' . $user->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    // only KPI employees can appear in this report, so non-KPI accounts
                    // (SMS_CENTER etc.) must not be selectable
                    return $er->createQueryBuilder('e')
                        ->join('e.userGroup', 'userGroup')
                        ->andWhere("userGroup.slug = :slug")->setParameter('slug', 'employee')
                        ->andWhere("e.userMode = :userMode")->setParameter('userMode', 'KPI')
                        ->orderBy('e.name', 'ASC');

                },
                'attr'=>[
                    'class'=>'select2'
                ],
                'placeholder' => '- Select Employee -',
                'required' => false,

            ])
            ->add('lineManager', EntityType::class, array(
                'required'    => false,
                'class' => User::class,
                'placeholder' => '- Select Line Manager -',
                'choice_label' => function($lineManager){
                    /** @var User $lineManager */
                    return  '('. $lineManager->getUserId() .') ' . $lineManager->getName();
                },
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('e');
                    $qb->where("e.enabled = 1")
                        ->andWhere('e.isDelete = 0')
                        ->andWhere($qb->expr()->orX(
                            $qb->expr()->like("e.roles", ':lineManager'),
                            $qb->expr()->like("e.roles", ':admin')
                        ))
                        ->setParameters([
                            'lineManager' => '%ROLE_LINE_MANAGER%',
                            'admin' => '%ROLE_KPI_ADMIN%'
                        ])
                        ->orderBy('e.name', 'ASC');
                    return $qb;
                },
            ))
            ->add('reportMode', EntityType::class, array(
                'required'     => false,
                'class' => Setting::class,
                'multiple'     => true,
                'expanded'     => false,
                'choice_label' => 'name',
                'attr'=>array('class'=>'select2', 'multiple'=>'multiple', 'data-placeholder'=>'- Select Report Mode -'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join('e.settingType', 'st')
                        ->where("st.slug = 'report-mode'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('filter', SubmitType::class,[
                'attr'=>[
                    'class' => 'btn btn-primary btn-block'
                ]

            ])

        ;
    }

    private function getYears($min, $max='current')
    {
//        $years = range($min, ($max === 'current' ? date('Y') : $max));
        $years = range(($max === 'current' ? date('Y') : $max), $min);
        return array_combine($years, $years);
    }

    private function otherReportUserWise($user)
    {
        $otherReport = [
            'Others' => [
                'Expense' => 'expense',
                'Feed Complain' => 'feed-complain'
            ]
        ];

        if (in_array('ROLE_CRM_POULTRY_USER', $user->getRoles()) || in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())) {
            $otherReport = array_merge(
                $otherReport,
                ['Poultry' => [
                    'Company Wise Feed Sale' => 'company-wise-feed-sale-poultry',
//                    'Company Wise Boiler Chicks' => 'company-wise-boiler-chick',
                    'DOC Production' => 'company-wise-layer-chick',
//                    'Complain' => 'complain-poultry',
                    'Farmer Survey' => 'farmer-survey-poultry',
                    'Farmer Training' => 'farmer-training-poultry',
                    'FCR Different Companies (Broiler)' => 'fcr-different-companies-poultry',
                    'FCR Different Companies (Sonali)' => 'fcr-different-companies-sonali',
                    'Lab Service' => 'lab-service-poultry',
                    'DOC Complain' => 'doc-complain',
                    ]
                ]
            );
        }
        if (in_array('ROLE_CRM_CATTLE_USER', $user->getRoles()) || in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())) {
            $otherReport = array_merge(
                $otherReport,
                ['Cattle' => [
                    'New Agent or Upgradation' => 'new-agent-upgradation-cattle',
//                    'Complain' => 'complain-cattle',
                    'Company Wise Feed Sale' => 'company-wise-feed-sale-cattle',
                    'Farmer Survey' => 'farmer-survey-cattle',
                    'Farmer Training' => 'farmer-training-cattle',

                ]
                ]
            );
        }
        if (in_array('ROLE_CRM_AQUA_USER', $user->getRoles()) || in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())) {
            $otherReport = array_merge(
                $otherReport,
                ['Fish' => [
                    'New Agent or Upgradation' => 'new-agent-upgradation-fish',
                    'Company Wise Feed Sale' => 'company-wise-feed-sale-fish',
                    'Farmer Survey' => 'farmer-survey-fish',
                    'Farmer Training' => 'farmer-training-fish',
                    'Fish Sales Price' => 'fish-sales-price',
                    'Tilapia Fry Sales' => 'fish-tilapia-fry-sales',
                ]
                ]
            );
        }
        if (in_array('ROLE_CRM_SALES_MARKETING_USER', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())) {
            $otherReport = array_merge(
                $otherReport,
                ['Sales & Marketing' =>
                    [
//                        'DOC Price' => 'doc-price',
//                        'Daily DOC Price' => 'doc-price-daily',
//                        'Meat & Egg Price' => 'meat-egg-price',
                    ]
                ]
            );
        }

        return $otherReport;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'loggedUser' => User::class,
            'userRepo' => UserRepository::class,
        ]);
    }
}