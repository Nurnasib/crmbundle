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
use App\Entity\Admin\Terminal;
use App\Entity\Core\Agent;
use App\Entity\User;
use App\Repository\Core\AgentRepository;
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
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

class CrmCustomerFormType extends AbstractType{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user =  $options['user']->getId();
        $builder
            ->add('name', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.name',
            ])
            ->add('mobile', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.mobile',
                'required' => false,
            ])
            ->add('address', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'required' => false,
                'row_attr' => ['class' => 'textarea', 'rows'=>2],
            ])

            ->add('agent',TextType::class,[
                'attr' => ['autofocus' => true],
                'required' => false,
            ])
            ->add('agent', EntityType::class, [
                'class' => Agent::class,
                'attr'=>['class'=>'span12 m-wrap select2'],
                'required'    => false,
                'choice_label' => 'idName',
                'placeholder' => 'Choose a agent',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.agentGroup","agentGroup")
                        ->orderBy('e.name', 'ASC');
                },
//                'choices'   => $options['agentRepo']->getLocationWiseAgentForm($options['user'])
            ])
            ->add('customerGroup', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => 'Choose a customer name',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er)use($user){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='CUSTOMER_GROUP'")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('location', EntityType::class, array(
                'required'    => false,
                'class' => Location::class,
                'placeholder' => 'Choose a  upozila name',
                'choice_label' => 'name',
                'group_by'  => 'parent.name',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'query_builder' => function(EntityRepository $er)use($user){
                    return $er->createQueryBuilder('e')
                        ->join("e.user","u")
                        ->join("e.parent","p")
                        ->andWhere("e.level =5")
                        ->orderBy('p.name', 'ASC');
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
            'data_class' => CrmCustomer::class,
            'user' => User::class,
            'agentRepo' => AgentRepository::class,
            //'markRepo' => MarkChartRepository::class,
        ]);
    }


}