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
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ChickLifeCycleFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('officer_name', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.officer_name',
            ])
            ->add('reportingDate', DateType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.reportingDate',
            ])
            ->add('visitingweek', EntityType::class, [
                'class' => Setting::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.status =1')
                        ->orderBy('e.name', 'ASC');
                },
                'attr'=>['class'=>'span12'],
                'choice_label' => 'name',
                'placeholder' => 'Choose a setting type',
            ])
            ->add('bird_mode', ChoiceType::class, [
                'choices'  => [
                    'Sonali' => 'SONALI',
                    'Broiler' => 'BROILER',
                ],
            ])


            ->add('region', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.region',
            ])->add('totalbirds', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.totalbirds',
            ])->add('age_days', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.age_days',
            ])->add('mortality_pes', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mortality_pes',
            ])->add('weightStandard', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.weightStandard',
            ])->add('weightAchieved', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.weightAchieved',
            ])->add('feedTotalkg', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedTotalkg',
            ])->add('feedBird', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedBird',
            ])->add('feedStandard', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedStandard',
            ])->add('fcr_with_mortality', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.fcr_with_mortality',
            ])->add('fcr_without_mortality', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.fcr_without_mortality',
            ])->add('hatchery', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.hatchery',
            ])->add('breed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.breed',
            ])->add('feed', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feed',
            ])->add('feedType', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.feedType',
            ])->add('proDate', DateType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.proDate',
            ])->add('batchNo', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.batchNo',
            ])->add('remarks', TextareaType::class, [
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
            'data_class' => ChickLifeCycle::class,
        ]);
    }
}