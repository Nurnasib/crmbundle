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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\CrmVisit;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CrmVisitFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user =  $options['user']->getId();
        $builder

            ->add('working_duration', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.working_duration',
            ])
            ->add('location', EntityType::class, array(
                'required'    => false,
                'class' => Location::class,
                'placeholder' => 'Choose a  upozila name',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er)use($user){
                    return $er->createQueryBuilder('e')
                        ->join("e.user","u")
                        ->andWhere("u.id ='{$user}'")
                        ->orderBy('e.name', 'ASC');
                },
            ))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CrmVisit::class,
            'user' => User::class,
        ]);
    }
}