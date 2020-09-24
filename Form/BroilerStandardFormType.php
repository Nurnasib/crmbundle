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
//use Terminalbd\CrmBundle\Entity\SettingType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class BroilerStandardFormType extends AbstractType
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
                'required' => true
            ])
            ->add('target_body_weight', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.target_body_weight',
                'required' => true
            ])
            ->add('target_feed_consumption', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.target_feed_consumption',
                'required' => true
            ])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BroilerStandard::class,
        ]);
    }
}