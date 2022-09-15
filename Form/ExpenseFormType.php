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


use App\Entity\Admin\Location;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

            ->add('conveyance', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.conveyance',
                'required' => false
            ])
            ->add('daily_allowance', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.daily_allowance',
                'required' => false
            ])->add('hotel_rent', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.hotel_rent',
                'required' => false
            ])->add('photostate', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.photostate',
                'required' => false
            ])->add('courier', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.courier',
                'required' => false
            ])->add('food', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.food',
                'required' => false
            ])->add('mobile', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mobile',
                'required' => false
            ])->add('maintenace', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.maintenace',
                'required' => false
            ])->add('toll_bill', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.toll_bill',
                'required' => false
            ])->add('service_charge', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.service_charge',
                'required' => false
            ])->add('others', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.others',
                'required' => false

            ])
            ->add('purpose', EntityType::class, [
                'class' => Setting::class,
                'required' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :sType')
                        ->setParameter('sType','PURPOSE')
                        ->orderBy('e.name','ASC');
                },
                'attr'=>['class'=>'span12 multi-select2'],
                'choice_label' => 'name',
                'placeholder' => 'Choose your Purpose',
            ])
            ->add('vehicle',EntityType::class,[
                'class' => Setting::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('e')
                    ->where("e.settingType = 'VEHICLE'");
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add($builder->create('expenseDate', TextType::class, array(
                'label' => 'Date',
                'required' => true,
                'attr' => array(
                    'class' => 'datePicker expense_date',
                    'autocomplete' => 'off',
                    'placeholder' => 'd-m-Y'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))


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