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


use App\Entity\Core\Agent;
use App\Entity\User;
use App\Form\Type\DateTimePickerType;
use Doctrine\ORM\EntityRepository;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class DiseaseMappingFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report'];
        $parentParent =  $report->getParent()->getParent();
        $builder
            ->add($builder->create('visitingDate', TextType::class, array(
                'label' => 'Hatching Date',
                'attr' => array(
                    'class' => 'datePicker disease_visiting_date',
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
            ->add('farmType', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Species',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($parentParent){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='SPECIES_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->andWhere("e.slug NOT LIKE :slug")
                        ->setParameter('parent',$parentParent)
                        ->setParameter('slug', 'others%')
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('disease', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Disease',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap disease'),
                'query_builder' => function(EntityRepository $er)use($parentParent){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='DISEASE_NAME'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$parentParent)
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
            ->add('breed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Breed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feed'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='BREED_TYPE'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('flockSizeOrCapacity', NumberType::class, [
                'attr' => ['min'=>0,'autofocus' => true],
                'label' => 'label.flockSizeOrCapacity',
                'required'=>false
            ])
            ->add('ageDays', NumberType::class, [
                'attr' => ['min'=>0, 'autofocus' => true],
                'label' => 'label.ageDays',
                'required'=>false,
            ])
            ->add('ageUnitType', ChoiceType::class, [
                'label' => 'label.ageUnitType',
                'required'=>false,
                'choices'  => [
                    'Day' => 'Day',
                    'Month' => 'Month',],
                'placeholder' => false,
            ])
            ->add('treatment', TextareaType::class, [
                'label' => 'label.treatment',
                'required'=>false,
            ])
            ->add('remarks', TextareaType::class, [
                'label' => 'label.remarks',
                'required'=>false,
            ])
            ->add('cultureAreaForFish', NumberType::class, [
                'attr' => ['min'=>0,'autofocus' => true],
                'label' => 'label.cultureAreaForFish',
                'required'=>false
            ])
            ->add('averageWeightForFish', NumberType::class, [
                'attr' => ['min'=>0,'autofocus' => true],
                'label' => 'label.averageWeightForFish',
                'required'=>false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiseaseMapping::class,
            'report' => Setting::class,
            'farmTypeId' => '',
        ]);
    }
}