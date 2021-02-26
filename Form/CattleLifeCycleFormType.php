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


use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CattleLifeCycleFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report']->getParent();
        $builder
            ->add('breed_type', EntityType::class, array(
                'class' => Setting::class,
                'placeholder' => 'Choose Breed Type',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($report){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='BREED_TYPE'")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$report)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('feed_type', EntityType::class, array(
                'class' => Setting::class,
                'placeholder' => 'Choose Feed Type',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er)use($report){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='FEED_TYPE'")
                        ->andWhere("e.status=1")
                        ->andWhere("e.parent = :parent")
                        ->setParameter('parent',$report)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add($builder->create('reporting_date', TextType::class, array(
                'label' => 'Reporting Date',
                'attr' => array(
                    'class' => 'datePicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'date-month-Year'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))

            ->add('remarks', TextareaType::class, [
                'attr' => ['autofocus' => true],
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
            'data_class' => CattleLifeCycle::class,
            'user' => User::class,
            'report' => Setting::class,
        ]);
    }
}