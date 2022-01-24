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
        $builder
            ->add('otherReport', ChoiceType::class,[
                'choices' => [
                    'Poultry' => [
                        'Farmer Survey' => 'farmer-survey-poultry',
                        'Lab Service' => 'lab-service-poultry',
                        'FCR Different Companies' => 'fcr-different-companies-poultry',
                        'Company Wise Feed Sale' => 'company-wise-feed-sale-poultry',

                    ],
                    'Cattle' => [
                        'Farmer Survey' => 'farmer-survey-cattle',
                        'Company Wise Feed Sale' => 'company-wise-feed-sale-cattle',

                    ],
                    'Fish' => [
                        'Farmer Survey' => 'farmer-survey-fish',
                        'Company Wise Feed Sale' => 'company-wise-feed-sale-fish',


                    ],

                ],
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
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT')
                        ->andWhere('e.slug NOT IN (:slug)')->setParameter('slug', ['sonali-life-cycle','boiler-life-cycle','layer-life-cycle-brown','layer-life-cycle-white','dairy-life-cycle','fattening-life-cycle','fish-life-cycle-report','fish-life-cycle-after-sale-report'])
                        ->andWhere('e.status = 1');
                },
                'attr' => [
                    'class' => 'select2'
                ]

            ])
            ->add('lifeCycle', EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'placeholder' => '- Select Life Cycle -',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT')
                        ->andWhere('e.slug IN (:slug)')->setParameter('slug', ['sonali-life-cycle','boiler-life-cycle','layer-life-cycle-brown','layer-life-cycle-white','dairy-life-cycle','fattening-life-cycle','fish-life-cycle-report','fish-life-cycle-after-sale-report'])
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
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->join('e.userGroup', 'userGroup')
                    ->where("userGroup.slug = 'employee'")
                    ->andWhere("e.enabled = 1")
                    ->orderBy('e.name');
                },
                'choice_label' => 'name',
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

    /**
     * {@inheritdoc}
     */
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => BroilerLifeCycle::class,
//        ]);
//    }
}