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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Fcr;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class SearchFilterFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', TextType::class,[
//                'mapped' => false,
                'attr'=>[
                    'placeholder' => 'Start Date (YYYY-mm-dd)',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('endDate', TextType::class,[
//                'mapped' => false,
                'attr'=>[
                    'placeholder' => 'End Date (YYYY-mm-dd)',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('startDateCreated', TextType::class,[
//                'mapped' => false,
                'attr'=>[
                    'placeholder' => 'Start Date (YYYY-mm-dd)',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('endDateCreated', TextType::class,[
//                'mapped' => false,
                'attr'=>[
                    'placeholder' => 'End Date (YYYY-mm-dd)',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('farmer', EntityType::class,[
                'class' => CrmCustomer::class,
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('setting.slug = :slug')->setParameter('slug', 'farmer')
                    ->leftJoin('e.customerGroup', 'setting')
                    ->orderBy('e.name');
                },
                'choice_label' => 'name',
                'placeholder' => 'Select Farmer'
            ])
            ->add('employee', EntityType::class,[
                'class' => User::class,
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->join('e.userGroup', 'userGroup')
                    ->where("userGroup.slug = 'employee'")
                    ->andWhere("e.enabled = 1")
                    ->orderBy('e.name');
                },
                'choice_label' => 'name',
                'placeholder' => 'Select Employee',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('month', ChoiceType::class,[
                'choices' => [
                    'January' => '01',
                    'February' => '02',
                    'March' => '03',
                    'April' => '04',
                    'May' => '05',
                    'June' => '06',
                    'July' => '07',
                    'August' => '08',
                    'September' => '09',
                    'October' => '10',
                    'November' => '11',
                    'December' => '12',
                ],
                'placeholder' => 'Select month',
                'required' => false
            ])
            ->add('year', ChoiceType::class,[
                'choices' => $this->getYears(2021),
                'placeholder' => 'Select year',
                'required' => false
            ])
            ->add('filter', SubmitType::class,[
                'attr'=>[
                    'class' => 'btn btn-primary btn-block'
                ]

            ])

        ;
    }

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));
        return array_combine($years, $years);
    }

    /**
     * {@inheritdoc}
     */
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => BroilerLifeCycle::class,
//        ]);
//    }
}