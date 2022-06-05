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
use Terminalbd\CrmBundle\Entity\Setting;
//use Terminalbd\CrmBundle\Entity\SettingType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class TrainingMaterialFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.name',
                'required' => true
            ])
            ->add('parent', EntityType::class, array(
                'required'    => true,
                'class' => Setting::class,
                'placeholder' => 'Choose Parent',
                'choice_label' => 'name',
                'attr'=>array('class'=>'select2 span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType = 'BREED_NAME'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('settingType', ChoiceType::class, [
                'required'    => true,
                'choices'  => [
                    'Training Material'=>'TRAINING_MATERIAL', //parent BREED_NAME (poultry, cattle, fish etc)
                ],
                'attr' => [
                    'class' => 'select2'
                ],
                'placeholder' => 'Select Type'
            ])

            ->add('status',CheckboxType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'checkboxToggle',
                    'data-toggle' => "toggle",
                    'data-style' => "slow",
                    'data-offstyle' => "warning",
                    'data-onstyle'=> "info",
                    'data-on' => "Enabled",
                    'data-off'=> "Disabled"
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}