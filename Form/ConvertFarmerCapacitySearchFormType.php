<?php

namespace Terminalbd\CrmBundle\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Filter form for the Convert Farmer Capacity reports (daily + monthly).
 *
 * Deliberately separate from SearchFilterFormType so these reports can be changed
 * without touching every other CRM report that shares that form.
 *
 * The line manager list mirrors the filter on /employee/list, minus its
 * ROLE_KPI_ADMIN clause -- that clause is what lets admin/developer accounts
 * (who manage nobody) into the list, and this filter must show line managers only.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ConvertFarmerCapacitySearchFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line_managers', EntityType::class, [
                'class' => User::class,
                'placeholder' => '- Select Line Manager -',
                'required' => false,
                'choice_label' => function ($lineManager) {
                    /** @var User $lineManager */
                    return '(' . $lineManager->getUserId() . ') ' . $lineManager->getName();
                },
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('e');
                    $qb->join('e.userGroup', 'userGroup');
                    $qb->where('e.enabled = 1')
                        ->andWhere('e.isDelete = 0')
                        ->andWhere("userGroup.slug = 'employee'")
                        ->andWhere("e.userMode = 'KPI'")
                        ->andWhere($qb->expr()->like('e.roles', ':lineManager'))
                        ->setParameter('lineManager', '%ROLE_LINE_MANAGER%')
                        ->orderBy('e.name', 'ASC');

                    return $qb;
                },
            ])
            ->add('employees', EntityType::class, [
                'class' => User::class,
                'placeholder' => '- All Employee -',
                'required' => false,
                'choice_label' => function ($employee) {
                    /** @var User $employee */
                    return '(' . $employee->getUserId() . ') ' . $employee->getName();
                },
                'attr' => [
                    'class' => 'select2'
                ],
                // same population as line_managers above, minus the ROLE_LINE_MANAGER clause:
                // the employee wise report lists every employee, not only the ones who manage others
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('e');
                    $qb->join('e.userGroup', 'userGroup');
                    $qb->where('e.enabled = 1')
                        ->andWhere('e.isDelete = 0')
                        ->andWhere("userGroup.slug = 'employee'")
                        ->andWhere("e.userMode = 'KPI'")
                        ->orderBy('e.name', 'ASC');

                    return $qb;
                },
            ])
            ->add('month', ChoiceType::class, [
                'choices' => $this->getMonths(),
                'placeholder' => '- Select month -',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Month is required.', 'groups' => ['month_only']]),
                ],
            ])
            ->add('start_month', ChoiceType::class, [
                'choices' => $this->getMonths(),
                'placeholder' => '- Select month -',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Start Month is required.', 'groups' => ['start_end_month_only']]),
                ],
            ])
            ->add('end_month', ChoiceType::class, [
                'choices' => $this->getMonths(),
                'placeholder' => '- Select month -',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'End Month is required.', 'groups' => ['start_end_month_only']]),
                ],
            ])
            ->add('year', ChoiceType::class, [
                'choices' => $this->getYears(2020),
                'placeholder' => '- Select year -',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Year is required.', 'groups' => ['year_only']]),
                ],
            ])
            ->add('farm_type', ChoiceType::class, [
                'choices' => [
                    'Poultry' => 'poultry-breed',
                    'Cattle' => 'cattle-breed',
                    'Fish' => 'fish-breed',
                ],
                'placeholder' => '- Select Type -',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Farm Type is required.', 'groups' => ['farm_type_only']]),
                ],
            ]);
    }

    private function getMonths()
    {
        return [
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
        ];
    }

    private function getYears($min, $max = 'current')
    {
        $years = range(($max === 'current' ? date('Y') : $max), $min);

        return array_combine($years, $years);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'validation_groups' => ['Default'],
        ]);
    }
}
