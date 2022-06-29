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
class SearchFilterFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['loggedUser'];
        $builder
            ->add('otherReport', ChoiceType::class,[
                'choices' => $this->otherReportUserWise($user),
                'placeholder' => '- Select Report -',
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('monthlyReport', EntityType::class,[
                'class' => Setting::class,
                'group_by'  => 'parent.parent.name',
                'choice_label' => 'name',
                'placeholder' => '- Select Report -',
                'query_builder' => function(EntityRepository $er) use($user){
                    $qb =  $er->createQueryBuilder('e');
                    $qb->join('e.parent', 'parent');
                    $qb->join('parent.parent', 'grand_parent');

                    $qb->where('e.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT');
                    $qb->andWhere('e.slug NOT IN (:lifeCycleSlug)')->setParameter('lifeCycleSlug', ['sonali-life-cycle','boiler-life-cycle','layer-life-cycle-brown','layer-life-cycle-white','dairy-life-cycle','fattening-life-cycle','fish-life-cycle-report','fish-life-cycle-after-sale-report']);
                    $qb->andWhere('e.status = 1');


                    $grandParentSlug = [];

                    if (in_array('ROLE_CRM_POULTRY_USER', $user->getRoles()) || in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                        $grandParentSlug = array_merge($grandParentSlug, ['poultry-breed']);
                    }
                    if (in_array('ROLE_CRM_CATTLE_USER', $user->getRoles()) || in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                        $grandParentSlug = array_merge($grandParentSlug, ['cattle-breed']);
                    }
                    if (in_array('ROLE_CRM_AQUA_USER', $user->getRoles()) || in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles()) || in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                        $grandParentSlug = array_merge($grandParentSlug, ['fish-breed']);
                    }
                    if (in_array('ROLE_LINE_MANAGER', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())){
                        $grandParentSlug = ['poultry-breed','cattle-breed','fish-breed'];
                    }

                    $qb->andWhere('grand_parent.slug IN (:grandParentSlug)')->setParameter('grandParentSlug', $grandParentSlug);
                    return $qb;
                },
                'attr' => [
                    'class' => 'select2'
                ]

            ])
            ->add('lifeCycle', EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'group_by' => 'parent.parent.name',
                'placeholder' => '- Select Life Cycle -',
                'query_builder' => function(EntityRepository $er) use($user){
                    $slug = [];

                    if (in_array('ROLE_CRM_POULTRY_USER', $user->getRoles()) || in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles())){
                        $slug = array_merge(['sonali-life-cycle','boiler-life-cycle','layer-life-cycle-brown','layer-life-cycle-white'], $slug);
                    }

                    if (in_array('ROLE_CRM_CATTLE_USER', $user->getRoles()) || in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles())){
                        $slug = array_merge(['dairy-life-cycle','fattening-life-cycle'], $slug);
                    }

                    if (in_array('ROLE_CRM_AQUA_USER', $user->getRoles()) || in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles())) {
                        $slug = array_merge(['fish-life-cycle-report', 'fish-life-cycle-after-sale-report'], $slug);
                    }
                    if (in_array('ROLE_LINE_MANAGER', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())) {
                        $slug = ['sonali-life-cycle','boiler-life-cycle','layer-life-cycle-brown','layer-life-cycle-white','dairy-life-cycle','fattening-life-cycle','fish-life-cycle-report','fish-life-cycle-after-sale-report'];
                    }

                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT')
                        ->andWhere('e.slug IN (:slug)')->setParameter('slug', $slug)
                        ->andWhere('e.status = 1')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ]

            ])
            ->add('lab', EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Lab -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'LAB_NAME')
                        ->andWhere('e.status = 1')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'required' => false

            ])
            ->add('feedMill', EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Feed Mill -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'FEED_MILL')
                        ->andWhere('e.status = 1')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'required' => false

            ])
            ->add('breed', EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Breed -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'BREED_TYPE')
                        ->andWhere('e.status = 1')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'required' => false

            ])
            ->add('region', EntityType::class,[
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Region -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.level = :level')->setParameter('level', 3)
                        ->andWhere('e.parent IS NOT NULL')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'required' => false

            ])
            ->add('zone', EntityType::class,[
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Region -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.level = :level')->setParameter('level', 2)
                        ->andWhere('e.parent IS NOT NULL')
                        ->orderBy('e.name');
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'required' => false

            ])
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
            ->add('startDateCreated', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('endDateCreated', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('farmer', EntityType::class,[
                'class' => CrmCustomer::class,
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('setting.slug = :slug')->setParameter('slug', 'farmer')
                    ->leftJoin('e.customerGroup', 'setting')
                    ->orderBy('e.name');
                },
                'choice_label' => 'name',
                'placeholder' => '- Select Farmer -'
            ])
            ->add('employeeWiseFarmer', ChoiceType::class,[
                'choices' => [],
                'attr' => [
                    'class' => 'select2'
                ],
                'placeholder' => '- Select Farmer -',
                'required' => false
            ])
            ->add('employee', EntityType::class,[
                'class' => User::class,
                'query_builder' => function(EntityRepository $repository) use($user){
                    $qb = $repository->createQueryBuilder('e');
                    $qb->join('e.userGroup', 'userGroup');
                    $qb->where("userGroup.slug = 'employee'");
                    $qb->andWhere("e.enabled = 1");

                    $rolesString = implode('_', $user->getRoles());

                    if (!str_contains($rolesString,'ADMIN')){
                        if (!in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                            $qb->andWhere('e.id = :employeeId')->setParameter('employeeId', $user->getId());
                        }else{
                            $qb->andWhere("e.lineManager = :lineManager")->setParameter('lineManager', $user);
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
                'placeholder' => '- Select Employee -',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('month', ChoiceType::class,[
                'choices' => [
                    'January' => '01',
                    'February' => '02',
                    'March' => '03',
                    'April' => '04',
                    'May' => '05',
                    'June' => '06',
                    'July' => '07',
                    'August' => '08',
                    'September' => '09',
                    'October' => '10',
                    'November' => '11',
                    'December' => '12',
                ],
                'placeholder' => '- Select month -',
                'required' => false
            ])
            ->add('year', ChoiceType::class,[
                'choices' => $this->getYears(2020),
                'placeholder' => '- Select year -',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('reportStatus', ChoiceType::class,[
                'choices' => [
                    'In Progress' => 'IN_PROGRESS',
                    'Complete' => 'COMPLETE',
                ],
            ])
            ->add('filter', SubmitType::class,[
                'attr'=>[
                    'class' => 'btn btn-primary btn-block'
                ]

            ])

        ;
    }

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));
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
                    'FCR Different Companies' => 'fcr-different-companies-poultry',
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
                        'DOC Price' => 'doc-price',
                        'Meat & Egg Price' => 'meat-egg-price',
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
        ]);
    }
}