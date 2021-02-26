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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class FcrDetailsForAfterFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $report =  $options['report']->getParent();

        $builder

            ->add('agent', ChoiceType::class, array(
                'required'    => false,
                'attr' => array(
                    'class' => 'agent',
                ),
                'placeholder' => 'Choose Agent',
                'choices'   => $options['agentRepo']->getLocationWiseForOptions($options['user'])
            ))
            ->add($builder->create('hatching_date', TextType::class, array(
                'label' => 'Hatching Date',
                'required' => false,
                'attr' => array(
                    'class' => 'datePicker hatching_date',
                    'autocomplete' => 'off',
                    'placeholder' => 'd-m-Y'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add('total_birds', NumberType::class, [
                'attr' => ['autofocus' => true,'class' => 'totalBirds','min'=>0],
                'label' => 'label.totalbirds',
                'required' => false,
            ])
            ->add('age_day', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'ageDays','min'=>0],
                'label' => 'label.age_day',
                'required' => false,
            ])
            ->add('mortality_pes', NumberType::class, [
                'attr' => ['autofocus' => true,'class' => 'mortalityPes','min'=>0],
                'label' => 'label.pes',
                'required' => false,
            ])
            ->add('weight', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'weightAchieved','min'=>0],
                'label' => 'label.weight',
                'required' => false,
            ])
            ->add('feed_consumption_total_kg', NumberType::class, [
                'attr' => ['autofocus' => true, 'class'=>'feedTotalKg','min'=>0],
                'label' => 'label.feed_consumption_total_kg',
                'required' => false,
            ])
            ->add('hatchery', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Hatchery',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap hatchery'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='HATCHERY'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('breed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Breed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($report){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='BREED_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$report)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('feed', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feed'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='FEED_NAME'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('remarks', TextType::class, [
                'attr' => ['autofocus' => true, 'class'=>'remarks'],
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
            'data_class' => FcrDetails::class,
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
            'report' => Setting::class,
        ]);
    }
}