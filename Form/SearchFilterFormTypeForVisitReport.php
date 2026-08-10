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
use Terminalbd\CrmBundle\Entity\Setting;
use function Doctrine\ORM\QueryBuilder;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class SearchFilterFormTypeForVisitReport extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['loggedUser'];
        $userRepo = $options['userRepo'];
        $builder

            ->add('startDate', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off',
                    'class' => 'datepicker'
                ],
                'required' => false
            ])
            ->add('endDate', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off',
                    'class' => 'datepicker'

                ],
                'required' => false
            ])
            ->add('employee', EntityType::class,[
                'class' => User::class,
                'query_builder' => function(EntityRepository $repository) use($user, $userRepo){
                    $qb = $repository->createQueryBuilder('e');
                    $qb->join('e.userGroup', 'userGroup');
                    $qb->where("userGroup.slug = 'employee'");
                    $qb->andWhere("e.enabled = 1");
                    $qb->andWhere("e.userMode = 'KPI'");

                    $rolesString = implode('_', $user->getRoles());

                    if (!str_contains($rolesString,'ADMIN')){
                        if (!in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                            $qb->andWhere('e.id = :employeeId')->setParameter('employeeId', $user->getId());
                        }else{
                            $employeeIds=$userRepo->getEmployeesByLineManager($user);
                                $qb->andWhere('e.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
//                            $qb->andWhere("e.lineManager = :lineManager")->setParameter('lineManager', $user);
                        }
                    }else{
                        $userRole = [];
                        if (in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_POULTRY_USER');
                        }
                        if (in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_CATTLE_USER');
                        }
                        if (in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_AQUA_USER');
                        }
                        if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
                        }
                        if($userRole){
                            $query = '';
                            foreach ($userRole as $key => $role) {
                                if ($key !== 0){
                                    $query .= " OR ";
                                }
                                $query .= "e.roles LIKE '%" . $role . "%'";

                            }
                            $qb->andWhere($query);
                        }

                    }

                    $qb->orderBy('e.name');
                    return $qb;
                },
                'choice_label' => function($employee){
                    /**  @var User $employee */
                return '(' . $employee->getUserId() . ') ' . $employee->getName();
                },
                'placeholder' => '- All Employee -',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('process', ChoiceType::class,[
                'choices' => [
                    'Farmer' => 'farmer',
                    'Agent' => 'agent',
                    'Sub Agent' => 'sub-agent',
                    'Other Agent' => 'other-agent',
                ],
                'placeholder' => '- Select Group -',
                'required' => false,
            ])
            ->add('agentType', ChoiceType::class,[
                'choices' => [
                    'All' => 'all',
                    'Agent' => 'agent',
                    'Other Agent' => 'other-agent',
                    'Sub Agent' => 'sub-agent',
                ],
                'data' => 'all',
                'required' => false,
            ])
            ->add('serviceMode', EntityType::class, array(
                'required'    => false,
                'class' => \App\Entity\Core\Setting::class,
                'placeholder' => '- Select Service Mode -',
                'choice_label' => 'name',
                'attr'=>array('class'=>'select2 span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.settingType","st")
                        ->where("st.slug ='service-mode'")
                        ->andWhere("e.status = 1")
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