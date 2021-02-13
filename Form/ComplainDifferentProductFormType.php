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
use Doctrine\ORM\EntityRepository;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\AntibioticFreeFarm;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ComplainDifferentProduct;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\DiseaseMapping;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ComplainDifferentProductFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $report =  $options['report'];
        $reportParentParentChild =  $options['farmTypeId'];
//        dd($parentParent);
        $builder
            ->add('productName', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Product Name',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap productName'),
                'query_builder' => function(EntityRepository $er)use($reportParentParentChild){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='PRODUCT_TYPE'")
                        ->andWhere("e.parent IN (:parent)")
                        ->setParameter('parent',$reportParentParentChild)
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('complains', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.complains',
                'required'=>false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ComplainDifferentProduct::class,
            'report' => Setting::class,
            'farmTypeId' => '',
        ]);
    }
}