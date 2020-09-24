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

use App\Entity\Admin\Terminal;
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
use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

class CrmCustomerFormType extends AbstractType{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ])->add('agentId',TextType::class,[
                'attr' => ['autofocus' => true],
                'required' => false,
            ])
            ->add('subagentId',TextType::class,[
                'attr' => ['autofocus' => true],
                'required' => false,
            ])->add('location',TextType::class,[
                'attr' => ['autofocus' => true],
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
            'data_class' => CrmCustomer::class,
            'terminal' => Terminal::class,
            //'markRepo' => MarkChartRepository::class,
        ]);
    }


}