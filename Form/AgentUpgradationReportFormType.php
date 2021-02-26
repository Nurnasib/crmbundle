<?php

namespace Terminalbd\CrmBundle\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\AgentUpgradationReport;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class AgentUpgradationReportFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('breedName', EntityType::class, array(
                'class' => Setting::class,
                'placeholder' => 'Choose Feed Type',
                'choice_label' => 'name',
                'attr'=>array('class'=>'span12 m-wrap breed'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType ='BREED_NAME'")
                        ->andWhere("e.status =1")
                        ->orderBy('e.name', 'ASC');
                },
            ))
            ->add('previousSaleTon', NumberType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.previousSaleTon',
                'required'=>false
            ])
            ->add('presentSaleTon', NumberType::class, [
                'attr' => ['autofocus' => true,'min'=>0],
                'label' => 'label.presentSaleTon',
                'required'=>false
            ])
            ->add('agentStatus', ChoiceType::class, [
                'choices'  => [
                    'New Agent' => AgentUpgradationReport::AGENT_STATUS_NEW,
                    'Upgrade Agent' => AgentUpgradationReport::AGENT_STATUS_UPGRADE,
                ],
            ])
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
            'data_class' => AgentUpgradationReport::class,
            'user' => User::class,
            'report' => Setting::class,
        ]);
    }
}