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


use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class FishLifeCycleDetailsFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report']->getParent();
        $reportParentParent =  $report->getParent();
        $builder
            ->add($builder->create('reporting_date', TextType::class, array(
                'label' => 'Reporting Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add($builder->create('stocking_date', TextType::class, array(
                'label' => 'Stocking Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add($builder->create('previous_sampling_date', TextType::class, array(
                'label' => 'previousSamplingDate',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add($builder->create('present_sampling_date', TextType::class, array(
                'label' => 'presentSamplingDate',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add('hatchery', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Hatchery',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap hatchery'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='HATCHERY'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('feed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feed'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='FEED_NAME'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('mainCultureSpecies', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feed'),
                'query_builder' => function(EntityRepository $er)use($reportParentParent){
                    return $er->createQueryBuilder('e')
//                        ->join('e.parent','p')
                        ->where("e.settingType ='SPECIES_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$reportParentParent)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('feed_type', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed Type',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feedType'),
                'query_builder' => function(EntityRepository $er)use($report){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='FEED_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$report)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('feed_item_name', TextType::class, [
                'attr' => ['autocomplete' => 'off'],
                'label' => 'label.feed_item_name',
                'required'=>false
            ])
            ->add('other_culture_species', TextType::class, [
                'attr' => ['autocomplete' => 'off'],
                'label' => 'label.feed_item_name',
                'required'=>false
            ])
            ->add('culture_area_decimal', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.culture_area_decimal',
                'required'=>false
            ])
            ->add('no_of_initial_fish', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.no_of_initial_fish',
                'required'=>false
            ])
            ->add('average_initial_weight', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.average_initial_weight',
                'required'=>false
            ])
            ->add('total_initial_weight', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.total_initial_weight',
                'required'=>false
            ])
            ->add('average_present_weight', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.average_present_weight',
                'required'=>false
            ])
            ->add('current_feed_consumption_kg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.current_feed_consumption_kg',
                'required'=>false
            ])
            ->add('farmer_remarks', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.farmer_remarks',
                'required' => false,
            ])
            ->add('employee_remarks', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.employee_remarks',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FishLifeCycleDetails::class,
            'user' => User::class,
            'report' => Setting::class,
        ]);
    }
}