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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CattleLifeCycleDetailsFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user =  $options['user']->getId();
        $builder
            ->add($builder->create('visiting_date', TextType::class, array(
                'label' => 'Visiting Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-YYYY'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))
            ->add('age_of_cattle_month', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.ageOfCattleMonth',
                'required'=>false
            ])
            ->add('previous_body_weight', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.previous_body_weight',
                'required'=>false
            ])
            ->add('present_body_weight', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.present_body_weight',
                'required'=>false
            ])
            ->add('duration_of_bwt_difference', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.duration_of_bwt_difference',
                'required'=>false
            ])
            ->add('consumption_feed_intake_ready_feed', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.consumption_feed_intake_ready_feed',
                'required'=>false
            ])
            ->add('consumption_feed_intake_conventional', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.consumption_feed_intake_conventional',
                'required'=>false
            ])
            ->add('fodder_green_grass_kg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.fodder_green_grass_kg',
                'required'=>false
            ])
            ->add('fodder_straw_kg', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.dm_of_fodder_straw_kg',
                'required'=>false
            ])
            ->add('remarks', TextType::class, [
                'attr' => ['autofocus' => true],
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
            'data_class' => CattleLifeCycleDetails::class,
            'user' => User::class,
        ]);
    }
}