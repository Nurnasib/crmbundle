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
use Terminalbd\CrmBundle\Entity\CostBenefitAnalysisForLessCostingFarm;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CostBenefitAnalysisLessCostingFarmForPoultryFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report']->getParent();
        $builder
            ->add($builder->create('hatching_date', TextType::class, array(
                'label' => 'Hatching Date',
                'attr' => array(
                    'class' => 'less_costing_farm_form_hatching_date datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add($builder->create('reporting_month', TextType::class, array(
                'label' => 'Reporting Month',
                'attr' => array(
                    'class' => 'less_costing_farm_form_reporting_month monthYearPicker',
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
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='HATCHERY'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('breed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Breed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($report){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='BREED_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$report)
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
            ->add('totalBroilerWeightKg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalBroilerWeightKg',
                'required'=>false
            ])
            ->add('mortality', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mortality',
                'required'=>false
            ])
            ->add('ageDays', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.ageDays',
                'required'=>false
            ])
            ->add('itemPricePerPcs', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.itemPricePerPcs',
                'required'=>false
            ])
            ->add('feedPricePerKg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedPricePerKg',
                'required'=>false
            ])
            ->add('broilerOrFishPricePerKg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.broilerOrFishPricePerKg',
                'required'=>false
            ])
            ->add('totalMedicineCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalMedicineCost',
                'required'=>false
            ])
            ->add('totalVaccineCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalVaccineCost',
                'required'=>false
            ])
            ->add('usedBagPricePerPcs', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.usedBagPricePerPcs',
                'required'=>false
            ])
            ->add('litterOrPondRentCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.litterOrPondRentCost',
                'required'=>false
            ])
            ->add('electricityAndFuelCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.electricityAndFuelCost',
                'required'=>false
            ])
            ->add('labourCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.labourCost',
                'required'=>false
            ])
            ->add('transportCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.transportCost',
                'required'=>false
            ])
            ->add('otherCost', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.otherCost',
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
            'data_class' => CostBenefitAnalysisForLessCostingFarm::class,
            'report' => Setting::class,
        ]);
    }
}