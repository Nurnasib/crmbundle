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
                'choice_label' => 'getNameType',
                'attr'=>array('class'=>'select2 span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
//                        ->where("e.parent IS NULL")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('settingType', ChoiceType::class, [
                'choices'  => [
                    'Agent Purpose' => 'AGENT_PURPOSE',
                    'Vehicle' => 'VEHICLE',
                    'Meat & Egg Type' => 'MEAT_EGG_TYPE',
                    'Farmer Purpose' => 'PURPOSE',
                    'Other Agent Purpose' => 'OTHER_AGENT_PURPOSE',
                    'Sub Agent Purpose' => 'SUB_AGENT_PURPOSE',
                    'Farm Type' => 'FARM_TYPE',
                    'Farm Capacity' => 'FARM_CAPACITY',
                    'Customer Group' => 'CUSTOMER_GROUP',
                    'Visiting_Week'=>'Visiting_Week',
                    'Designation'=>'Designation',
                    'Farmer Report'=>'FARMER_REPORT', //parent FARM_TYPE (broiler, sonali, layer, dairy, fattening etc)
                    'Hatchery'=>'HATCHERY',
                    'Breed Name'=>'BREED_NAME',
                    'Breed Type'=>'BREED_TYPE', //parent FARM_TYPE (broiler, sonali, layer, dairy, fish, fattening etc)
                    'Feed Type'=>'FEED_TYPE',  //parent FARM_TYPE (broiler, sonali, layer, dairy, fish, fattening etc)
                    'Feed Name'=>'FEED_NAME',
                    'Feed Mill'=>'FEED_MILL',
                    'Species Type'=>'SPECIES_TYPE', //parent BREED_NAME (poultry, cattle, fish etc)
                    'Species Name'=>'SPECIES_NAME', //parent FEED_TYPE (Floating, Sinking)
                    'Fish Size'=>'FISH_SIZE', //parent SPECIES_TYPE (Tilapia, Pangas etc.)
                    'Color'=>'COLOR',
                    'Chick Type'=>'CHICK_TYPE',
                    'Training Material'=>'TRAINING_MATERIAL', //parent BREED_NAME (poultry, cattle, fish etc)
                    'Disease Name'=>'DISEASE_NAME', //parent BREED_NAME (poultry, cattle, fish etc)
                    'Product Type'=>'PRODUCT_TYPE', //parent FARM_TYPE (broiler, sonali, layer, dairy, fattening etc)
                    'Lab Name'=>'LAB_NAME', //parent FARM_TYPE (broiler, sonali, layer, dairy, fattening etc)
                    'Lab Service Name'=>'LAB_SERVICE_NAME', //parent FARM_TYPE (broiler, sonali, layer, dairy, fattening etc)
                    'Complain Doc'=>'COMPLAIN_DOC',
                    'Transport'=>'TRANSPORT',
                    'Complain Feed'=>'COMPLAIN_FEED',
                    'Nourish Hatchery'=>'HATCHERY_NOURISH',
                    'Complain Type'=>'COMPLAIN_TYPE',
                    'Working Mode'=>'WORKING_MODE',

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