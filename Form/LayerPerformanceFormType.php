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
use App\Form\Type\DateTimePickerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\LayerPerformance;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerPerformanceFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cso', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.cso',
            ])
            ->add('designation', ChoiceType::class, [
                'choices'  => [
                    'Doctor' => 'Doctor',
                    'Sales Force' => 'Sale_force',
                ]
            ])
            ->add('total_birds', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off' ],
                'label' => 'label.total_birds',
            ])->add('age_wk', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.age_wk',
            ])
            ->add('bird_weight_achieved', TextType::class, [
                'attr' => ['autofocus' => true ,'autocomplete' => 'off'],
                'label' => 'label.bird_weight_achieved',
            ])
            ->add('bird_weight_target', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.bird_weight_target',
            ])
            ->add('feed_intake_per_bird', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed_intake_per_bird',
            ])
            ->add('feed_Target', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed_Target',
            ])
            ->add('egg_production_achieved', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_production_achieved',
            ])
            ->add('egg_production_target', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_production_target',
            ])
            ->add('egg_weight_achieved', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_weight_achieved',
            ])
            ->add('egg_weight_stand', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_weight_stand',
            ])
            ->add('feed_type', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed_type',
            ])
            ->add('production_date', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'date-picker col-md-11','autocomplete' => 'off'],
                'label' => 'label.feed_type',
            ])
            ->add('month', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'dateCalendar col-md-11','autocomplete' => 'off'],
                'label' => 'label.month',
            ])
            ->add('region', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.region',
            ])

            ->add('batch_no', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.batch_no',
            ])
            ->add('feed_mill', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.feed_mill',
            ])
            ->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.hatchery',
            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
            ])->add('color', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.color',
            ])
            ->add('disease', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.disease',
            ])
            ->add('remarks', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.remarks',
            ])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LayerPerformance::class,
        ]);
    }
}