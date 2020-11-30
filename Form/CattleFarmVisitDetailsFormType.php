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
use App\Repository\Core\AgentRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CattleFarmVisitDetailsFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $report =  $options['report']->getParent();

        $builder
            ->add($builder->create('visiting_date', TextType::class, array(
                'label' => 'Visiting Date',
                'required' => false,
                'attr' => array(
                    'class' => 'datePicker visiting_date',
                    'autocomplete' => 'off',
                    'placeholder' => 'd-m-Y'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add('cattlePopulationOx', NumberType::class, [
                'attr' => ['autofocus' => true,'class' => 'cattlePopulationOx','min'=>0],
                'label' => 'label.cattlePopulationOx',
                'required' => false,
            ])
            ->add('cattlePopulationCow', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'cattlePopulationCow','min'=>0],
                'label' => 'label.cattlePopulationCow',
                'required' => false,
            ])
            ->add('cattlePopulationCalf', NumberType::class, [
                'attr' => ['autofocus' => true,'class' => 'cattlePopulationCalf','min'=>0],
                'label' => 'label.cattlePopulationCalf',
                'required' => false,
            ])
            ->add('avgMilkYieldPerDay', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'avgMilkYieldPerDay','min'=>0],
                'label' => 'label.avgMilkYieldPerDay',
                'required' => false,
            ])
            ->add('conceptionRate', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'conceptionRate','min'=>0],
                'label' => 'label.conceptionRate',
                'required' => false,
            ])
            ->add('fodderGreenGrassKg', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'fodderGreenGrassKg','min'=>0],
                'label' => 'label.fodderGreenGrassKg',
                'required' => false,
            ])
            ->add('fodderStrawKg', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'fodderStrawKg','min'=>0],
                'label' => 'label.fodderStrawKg',
                'required' => false,
            ])
            ->add('typeOfConcentrateFeed', TextType::class, [
                'attr' => ['autofocus' => true, 'class'=>'batchNo'],
                'label' => 'label.remarks',
                'required' => false,
            ])
            ->add('marketPriceMilkPerLiter', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'marketPriceMilkPerLiter','min'=>0],
                'label' => 'label.marketPriceMilkPerLiter',
                'required' => false,
            ])
            ->add('marketPriceMeatPerKg', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'marketPriceMeatPerKg','min'=>0],
                'label' => 'label.marketPriceMeatPerKg',
                'required' => false,
            ])
            ->add('remarks', TextType::class, [
                'attr' => ['autofocus' => true, 'class'=>'remarks'],
                'label' => 'label.remarks',
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
            'data_class' => CattleFarmVisitDetails::class,
            'user' => User::class,
            'report' => Setting::class,
        ]);
    }
}