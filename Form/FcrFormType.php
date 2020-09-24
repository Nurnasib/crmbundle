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
use Terminalbd\CrmBundle\Entity\Fcr;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class FcrFormType extends AbstractType
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
            ->add('fcr_of_feed', ChoiceType::class, [
                'choices'  => [
                    'AFTER' => 'AFTER',
                    'BEFORE' => 'BEFORE',
                ]
            ])
            ->add('reporting_month', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'dateCalendar col-md-11', 'placeholder' => 'Reporting Month','autocomplete' => 'off' ],
                'label' => 'label.reporting_month',
            ])
            ->add('hatching_date', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'date-picker col-md-11', 'placeholder' => 'Reporting Date','autocomplete' => 'off'],
                'label' => 'label.hatching_date',
            ])
            ->add('totalbirds', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalbirds',
            ])->add('age_day', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.age_day',
            ])
            ->add('pes', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.pes',
            ])
            ->add('weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.weight',
            ])
            ->add('total_feed_consumption', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.total_feed_consumption',
            ])
            ->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.hatchery',
            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
            ])->add('feed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed',
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
            'data_class' => Fcr::class,
        ]);
    }
}