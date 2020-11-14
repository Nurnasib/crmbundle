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
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
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
            ->add($builder->create('reporting_month', TextType::class, array(
                'label' => 'Reporting Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'date-month-Year'
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
            'data_class' => Fcr::class,
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
        ]);
    }
}