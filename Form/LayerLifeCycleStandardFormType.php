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
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerLifecycleStandard;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifeCycleStandardFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('age', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.age',
            ])
            ->add('bird_type', ChoiceType::class, [
                'choices'  => [
                    'Shaver Star Cross 579' => 'ShaverStarCross579',
                    'Bovans White' => 'BovansWhite',
                ],
                'placeholder' => 'Select Bird Type',
            ])
            ->add('dailyFeedConsumption', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.dailyFeedConsumption',
                'required' => false,
            ])
            ->add('cumilative_feed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.cumilative_feed',
                'required' => false,
            ])

            ->add('maximum_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.maximum_weight',
                'required' => false,
            ])
            ->add('minimum_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.minimum_weight',
                'required' => false,
            ])


            ->add('body_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.body_weight',
                'required' => false,
            ])
            ->add('egg_production', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_production',
                'required' => false,
            ])
            ->add('egg_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.egg_weight',
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
            'data_class' => LayerLifecycleStandard::class,
        ]);
    }
}