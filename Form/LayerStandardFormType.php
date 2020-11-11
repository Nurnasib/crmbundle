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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
//use Terminalbd\CrmBundle\Entity\SettingType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\Setting;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerStandardFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('breed', EntityType::class, array(
                'required'    => true,
                'class' => Setting::class,
                'placeholder' => 'Choose Breed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='BREED_TYPE'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('target_body_weight', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.target_body_weight',
                'required' => true
            ])
            ->add('target_feed_consumption', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.target_feed_consumption',
                'required' => true
            ])
            ->add('target_egg_production', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.target_egg_production',
                'required' => false
            ])
            ->add('target_egg_weight', NumberType::class, [
                'attr' => ['autofocus' => true,'autocomplete' => 'off'],
                'label' => 'label.target_egg_weight',
                'required' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LayerStandard::class,
        ]);
    }
}