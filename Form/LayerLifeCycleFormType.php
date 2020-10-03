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
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifeCycleFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('total_birds', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.total_birds',
            ])
            ->add('hatchery_date', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'date-picker col-md-11','autocomplete' => 'off'],
                'label' => 'label.hatchery_date',
            ])
            ->add('visiting_date', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'date-picker col-md-11','autocomplete' => 'off'],
                'label' => 'label.visiting_date',
            ])
            ->add('age_week', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.age_week',
            ])


            ->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.hatchery',
            ])
            ->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
            ])
            ->add('dead_bird', TextType::class, [
                'attr' => ['autofocus' => true ,'autocomplete' => 'off'],
                'label' => 'label.dead_bird',
            ])

            ->add('avg_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.avg_weight',
            ])
            ->add('target_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.target_weight',
            ])
            ->add('uniformity', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.uniformity',
            ])
            ->add('feed_per_bird', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed_per_bird',
            ])
            ->add('target_feed_per_bird', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.target_feed_per_bird',
            ])
            ->add('total_eggs', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.total_eggs',
            ])
            ->add('target_egg_production', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.target_egg_production',
            ])
            ->add('egg_weight_actual', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_weight_actual',
            ])
            ->add('egg_weight_standard', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_weight_standard',
            ])
            ->add('feed_type', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed_type',
            ])

            ->add('production_date', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'date-picker col-md-11','autocomplete' => 'off'],
                'label' => 'label.feed_type',
            ])

            ->add('batch_no', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.batch_no',
            ])
            ->add('feed_mill', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.feed_mill',
            ])

            ->add('medicine', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.medicine',
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
            'data_class' => LayerLifeCycle::class,
        ]);
    }
}