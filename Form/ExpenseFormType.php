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


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Terminalbd\CrmBundle\Entity\Expense;
use Terminalbd\CrmBundle\Entity\Setting;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ExpenseFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('schedule_visit', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.schedule_visit',
                'required' => true
            ])
            ->add('visiting_area', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.visiting_area',
                'required' => true
            ])
            ->add('conveyance', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.conveyance',
                'required' => true
            ])
            ->add('daily_allowance', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.daily_allowance',
                'required' => true
            ])->add('hotel_rent', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.hotel_rent',
                'required' => true
            ])->add('photostate', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.photostate',
                'required' => true
            ])->add('courier', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.courier',
                'required' => true
            ])->add('food', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.food',
                'required' => true
            ])->add('mobile', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mobile',
                'required' => true
            ])->add('maintenace', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.maintenace',
                'required' => true
            ])->add('toll_bill', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.toll_bill',
                'required' => true
            ])->add('service_charge', TextType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.service_charge',
                'required' => true
            ])->add('others', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.others',
                'required' => false

            ])
            ->add('setting', EntityType::class, [
                'class' => Setting::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :sType')
                        ->setParameter('sType','PURPOSE')
                        ->orderBy('e.name','ASC');
                },
                'attr'=>['class'=>'span12'],
                'choice_label' => 'name',
                'placeholder' => 'Choose your Purpose',
            ])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
        ]);
    }
}