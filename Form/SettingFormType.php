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
class SettingFormType extends AbstractType
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
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Parent',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.parent IS NULL")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('settingType', ChoiceType::class, [
                'choices'  => [
                    'Farmer Purpose' => 'PURPOSE',
                    'Agent Purpose' => 'AGENT_PURPOSE',
                    'Other Agent Purpose' => 'OTHER_AGENT_PURPOSE',
                    'Sub Agent Purpose' => 'SUB_AGENT_PURPOSE',
                    'Farm Type' => 'FARM_TYPE',
                    'Farm Capacity' => 'FARM_CAPACITY',
                    'Customer Group' => 'CUSTOMER_GROUP',
                    'Visiting_Week'=>'Visiting_Week',
                    'Designation'=>'Designation',
                    'Farmer Report'=>'FARMER_REPORT',
                    'Hatchery'=>'HATCHERY',
                    'Breed Type'=>'BREED_TYPE',
                    'Feed Type'=>'FEED_TYPE',
                    'Feed Name'=>'FEED_NAME',
                    'Feed Mill'=>'FEED_MILL',
                    'Fish Name'=>'FISH_NAME',
                    'Color'=>'COLOR',
                    'Chick Type'=>'CHICK_TYPE',
                ],
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