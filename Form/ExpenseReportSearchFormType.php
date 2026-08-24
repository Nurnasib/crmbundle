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
use App\Entity\Core\Company;
use App\Entity\User;
use App\Repository\UserRepository;
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
class ExpenseReportSearchFormType extends AbstractType
{
    /** Heading and picker label for the merged company. */
    public const MERGED_COMPANY_LABEL = 'Nourish Poultry & Hatchery';

    /** Submitted in place of a core_company id; ExpenseController resolves it. */
    public const MERGED_COMPANY_VALUE = 'merged';


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Nourish Agro, Nourish Feeds and Nourish Poultry & Hatchery are reported as
            // one company here, so the picker offers the group rather than the three
            // rows of core_company. ExpenseController expands this value into the
            // company ids the three report queries filter on.
            ->add('company', ChoiceType::class, [
                'choices' => [
                    self::MERGED_COMPANY_LABEL => self::MERGED_COMPANY_VALUE,
                ],
                'mapped' => false,
                'required' => true,
                'placeholder' => false,
                'data' => self::MERGED_COMPANY_VALUE,
                'attr'=>array('class'=>'span12 m-wrap'),
            ])
            ->add($builder->create('visitDate', TextType::class, array(
                'label' => 'Date',
                'required' => true,
//                'mapped' => false,
                'attr' => array(
                    'class' => 'visit_date monthYearPicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'm-Y'
                ),
//                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'm-Y')))
//            ->add('Save', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}