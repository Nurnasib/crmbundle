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
use Terminalbd\CrmBundle\Entity\CrmCustomer;
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
                'required' => false,
            ])
            ->add('production_date', TextType::class, [
                'attr' => ['autofocus' => true,'class'=>'dateCalendar col-md-11','autocomplete' => 'off'],
                'label' => 'label.feed_type',
                'required' => false,
            ])
            ->add('batch_no', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.batch_no',
                'required' => false,
            ])
            ->add('feed_mill', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.feed_mill',
                'required' => false,
            ])
            ->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.hatchery',
                'required' => false,
            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
                'required' => false,
            ])->add('color', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.color',
                'required' => false,
            ])
            ->add('disease', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.disease',
                'required' => false,
            ])
            ->add('remarks', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.remarks',
                'required' => false,
            ])
            ->add('customer', EntityType::class, [
                'class' => CrmCustomer::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->join('e.customerGroup','setting')
                        ->where('setting.slug = :farmer')
                        ->setParameter('farmer','farmer')
                        ->orderBy('e.name','ASC');
                },
                'attr'=>['class'=>'span12 select2'],
                'choice_label' => 'name',
                'placeholder' => 'Enter Farmer Name',
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
            'data_class' => LayerPerformance::class,
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
        ]);
    }
}