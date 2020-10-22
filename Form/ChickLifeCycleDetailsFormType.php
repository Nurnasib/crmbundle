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
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ChickLifeCycleDetailsFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user =  $options['user']->getId();
        $builder

            ->add('reportingDate', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'date-picker','autocomplete' => 'off'],
                'label' => 'label.reportingDate',
            ])
            ->add('hatching_date', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'date-picker','autocomplete' => 'off'],
                'label' => 'label.hatching_date',
            ])
         /*   ->add('visitingweek', EntityType::class, [
                'class' => CrmCustomer::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.customerGroup = :sCustomGroup')
                        ->setParameter('sCustomGroup','Farmer')
                        ->andWhere('isWeek=1')
                        ->orderBy('e.mobile','ASC');
                },
                'attr'=>['class'=>'span12 select2'],
                'choice_label' => 'weekName',
                'placeholder' => 'Week',
            ])*/
            ->add('visitingweek', ChoiceType::class, [
                'required' => true,
                'choices'  => [
                    '1st Week' => '1st',
                    '2nd Week' => '2nd',
                    '3rd Week' => '3rd',
                    '4th Week' => '4th',
                    '5th Week' => '5th',
                    '6th Week' => '6th',
                    '7th Week' => '7th',
                    '8th Week' => '8th',
                    '9th Week' => '9th',
                    '10th Week' =>'10th',
                ],

                'attr'=>['class'=>'span12'],
                'placeholder' => 'Choose Visiting Week',
            ])

            ->add('bird_mode', ChoiceType::class, [
                'choices'  => [
                    'Sonali' => 'SONALI',
                    'Broiler' => 'BROILER',
                ],
                'placeholder' => 'Bird Type',
            ])
              ->add('totalbirds', TextType::class, [
                'attr' => ['autofocus' => true ,'autocomplete' => 'off','class' => 'totalBirds'],
                'label' => 'label.totalbirds',
                'required' => false,
            ])
            ->add('age_days', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.age_days',
                'required' => false,
            ])
            ->add('mortality_pes', TextType::class, [
                'attr' => ['autofocus' => true,'class' => 'mortality_pes'],
                'label' => 'label.mortality_pes',
                'required'=>false
            ])
            ->add('mortality_percent', HiddenType::class, [
                'attr' => ['autofocus' => true,'class' => 'mortality_percent'],
                'label' => 'label.mortality_percent',
                'required'=>false
            ])

            ->add('weightStandard', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.weightStandard',
                'required' => false,
            ])
            ->add('weightAchieved', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'weightAchieved'],
                'label' => 'label.weightAchieved',
                'required' => false,
            ])
            ->add('feedTotalkg', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'feedTotalkg'],
                'label' => 'label.feedTotalkg',
            ])
            ->add('perBird', HiddenType::class, [
                'attr' => ['autofocus' => true,'class' => 'perBird'],
                'label' => 'label.perBird',
                'required'=>false
            ])
             ->add('withMortality', HiddenType::class, [
                'attr' => ['autofocus' => true,'class' => 'withMortality'],
                'label' => 'label.withMortality',
                'required'=>false
            ])
            ->add('withoutMortality', HiddenType::class, [
                'attr' => ['autofocus' => true,'class' => 'withoutMortality'],
                'label' => 'label.withoutMortality',
                'required'=>false
            ])
            ->add('feedStandard', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedStandard',
                'required' => false,
            ])
            ->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.hatchery',
                'required'=>false,

            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
                'required'=>false

            ])->add('feed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed',
                'required'=>false
            ])->add('feedType', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedType',
                'required'=>false
            ])->add('proDate', TextType::class, [
                'attr' => ['autofocus' => true ,'class'=>'date-picker','autocomplete' => 'off'],
                'label' => 'label.proDate',
                'required' => false,
            ])->add('batchNo', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.batchNo',
                'required' => false,
            ])->add('remarks', TextareaType::class, [
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
                'attr'=>['class'=>'span12 '],
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
            'data_class' => ChickLifeCycleDetails::class,
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
        ]);
    }
}