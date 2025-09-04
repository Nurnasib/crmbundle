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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('visitLocation', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.visitLocation',
                'required' => true
            ])
            ->add('workingArea', EntityType::class, [
                'class' => Setting::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.settingType = :sType')
                        ->setParameter('sType','AREA')
                        ->orderBy('e.name','ASC');
                },
                'attr'=>['class'=>'span12 select2'],
                'choice_label' => 'name',
                'placeholder' => 'Choose Area',
            ])
            //comments field
            ->add('comments', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.comments',
                'required' => false
            ])

            ->add($builder->create('expenseDate', TextType::class, array(
                'label' => 'Date',
                'required' => true,
                'attr' => array(
                    'class' => 'expense_date',
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