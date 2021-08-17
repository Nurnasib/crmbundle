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
use Terminalbd\CrmBundle\Entity\FishFeedComplain;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class FishFeedComplainFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('feedMaill', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose Feed Mill',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap feedMaill'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->andWhere("e.settingType ='FEED_MILL'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('performanceRelatedComplain', TextareaType::class, [
                'attr' => ['autofocus' => true,'rows'=>3],
                'label' => 'label.performanceRelatedComplain',
                'required'=>false
            ])
            ->add('physicalApperanceRelatedComplain', TextareaType::class, [
                'attr' => ['autofocus' => true,'rows'=>3],
                'label' => 'label.physicalApperanceRelatedComplain',
                'required'=>false
            ])
            ->add('feedItem', TextareaType::class, [
                'attr' => ['autofocus' => true,'rows'=>3],
                'label' => 'label.feedItem',
                'required'=>false
            ])
            ->add('feedItemDetails', TextareaType::class, [
                'attr' => ['autofocus' => true,'rows'=>3],
                'label' => 'label.feedItemDetails',
                'required'=>false
            ])
            ->add('remarks', TextareaType::class, [
                'attr' => ['autofocus' => true,'rows'=>3],
                'label' => 'label.remarks',
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
            'data_class' => FishFeedComplain::class,
            'report' => Setting::class,
            'farmTypeId' => '',
        ]);
    }
}