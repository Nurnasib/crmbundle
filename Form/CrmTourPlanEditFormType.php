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
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitPlan;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CrmTourPlanEditFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('visitingArea', TextType::class, [
                'label' => 'Visiting Area',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Visiting Area'
                ]
            ])
            ->add('workingMode', EntityType::class, [
                'class' => Setting::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.settingType = :type')
                        ->setParameter('type', 'WORKING_MODE')
                        ->andWhere('s.status = :status')
                        ->setParameter('status', 1)
                        ->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'Working Mode',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('agentInfo', TextareaType::class, [
                'label' => 'Agent Info',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Agent'
                ]
            ])
            ->add('Save', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CrmVisitPlan::class
        ]);
    }
}