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
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class AntibioticFreeFarmFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report']->getParent();
        $reportParentParentChild =  $options['farmTypeId'];
        $builder
            ->add($builder->create('hatching_date', TextType::class, array(
                'label' => 'Hatching Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add($builder->create('reporting_month', TextType::class, array(
                'label' => 'Reporting Month',
                'attr' => array(
                    'class' => 'monthYearPicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'm-Y')))

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
            ->add('breed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Breed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($reportParentParentChild){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='BREED_TYPE'")
                        ->andWhere("e.parent IN (:parent)")
                        ->setParameter('parent',$reportParentParentChild)
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
            ->add('totalStockedChicksPcs', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalStockedChicksPcs',
                'required'=>false
            ])
            ->add('totalFeedUsedKg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalFeedUsedKg',
                'required'=>false
            ])
            ->add('ageDays', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.ageDays',
                'required'=>false
            ])
            ->add('mortality', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mortality',
                'required'=>false
            ])
            ->add('totalBroilerWeightKg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalBroilerWeightKg',
                'required'=>false
            ])
            ->add('medicineTotalCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.medicineTotalCost',
                'required'=>false
            ])
            ->add('vaccineTotalCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.vaccineTotalCost',
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
            'data_class' => AntibioticFreeFarm::class,
            'report' => Setting::class,
            'farmTypeId' => '',
        ]);
    }
}