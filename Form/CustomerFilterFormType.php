<?php
/**
 * Created by PhpStorm.
 * User: Hasan
 * Date: 01/12/2022
 * Time: 3:30 PM
 */
namespace Terminalbd\CrmBundle\Form;


use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\Setting;

class CustomerFilterFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

            $builder
                ->add('customerId', TextType::class,[
                'attr' => [
                    'placeholder' => '- Farmer Id -'
                ],
                'required' => false
            ])
                ->add('customerName', TextType::class,[
                'attr' => [
                    'placeholder' => '- Farmer Name -'
                ],
                'required' => false
            ])
                ->add('customerMobile', TextType::class,[
                'attr' => [
                    'placeholder' => '- Mobile Number -'
                ],
                'required' => false
            ])
                ->add('customerAddress', TextType::class,[
                'attr' => [
                    'placeholder' => '- Address -'
                ],
                'required' => false
            ])
                ->add('feedCompany', EntityType::class, array(
                'required'    => false,
                'class' => Setting::class,
                'placeholder' => '- Select Feed -',
                'choice_label' => 'name',
                'attr'=>array('class'=>'select2 span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.settingType =:settingType")->setParameter('settingType','FEED_NAME')
                        ->andWhere("e.status = 1")
                        ->orderBy('e.sortOrder', 'ASC');
                },
                ))
                //status
                ->add('status', ChoiceType::class, [
                    'choices'  => [
                        'Active' => 'active',
                        'Close' => 'close',
//                        'Delete' => 'delete'
                    ],
                    'placeholder' => '- Select Status -',
                    'required' => false,
                    'attr'=>array('class'=>'select2 span12 m-wrap'),
                ])

            ->setMethod('get')
        ;
     }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }


}