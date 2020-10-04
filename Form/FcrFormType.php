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

            ->add('fcr_of_feed', ChoiceType::class, [
                'choices'  => [
                    'AFTER' => 'AFTER',
                    'BEFORE' => 'BEFORE',
                ]
            ])
            ->add('reporting_month', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'dateCalendar col-md-10', 'placeholder' => 'Reporting Month','autocomplete' => 'off' ],
                'label' => 'label.reporting_month',
            ])
            ->add('hatching_date', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'dateCalendar col-md-10', 'placeholder' => 'Reporting Date','autocomplete' => 'off'],
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
                'required' => false,
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
                'required' => false,
            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
                'required' => false,
            ])->add('feed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed',
                'required' => false,
            ])
            ->add('remarks', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.remarks',
                'required' => false,
            ])
            ->add('agent', EntityType::class, [
                'class' => Agent::class,
                'attr'=>['class'=>'span12'],
                'required'    => false,
                'choice_label' => 'name',
                'placeholder' => 'Choose a agent',
                'choices'   => $options['agentRepo']->getLocationWiseAgentForm($options['user'])
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
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
        ]);
    }
}