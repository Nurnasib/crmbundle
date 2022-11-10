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
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\Setting;
use function Doctrine\ORM\QueryBuilder;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class SearchFilterFormForSalesAndMarketingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['loggedUser'];
        $userRepo = $options['userRepo'];
        $builder
            /*->add('report', ChoiceType::class,[
                'choices' => $this->reportUserWise($user),
                'placeholder' => '- Select Report -',
                'attr' => [
                    'class' => 'select2'
                ]
            ])*/
            ->add($builder->create('startMonth', TextType::class, array(
                'label' => 'Start Month',
                'attr' => array(
                    'class' => 'monthYearPicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'mm-YYYY'
                ),
                'required' => false
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'm-Y')))

            ->add($builder->create('endMonth', TextType::class, array(
                'label' => 'End Month',
                'attr' => array(
                    'class' => 'monthYearPicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'mm-YYYY'
                ),
                'required' => false
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'm-Y')))

            ->add('startDate', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off',
                    'class' => 'datepicker'
                ],
                'required' => false
            ])
            ->add('endDate', TextType::class,[
                'attr'=>[
                    'placeholder' => 'dd-mm-YYYY',
                    'autocomplete' => 'off',
                    'class' => 'datepicker'

                ],
                'required' => false
            ])

            ->add('employee', EntityType::class,[
                'class' => User::class,
                'query_builder' => function(EntityRepository $repository) use($user, $userRepo){
                    $qb = $repository->createQueryBuilder('e');
                    $qb->join('e.userGroup', 'userGroup');
                    $qb->where("userGroup.slug = 'employee'");
                    $qb->andWhere("e.enabled = 1");

                    $rolesString = implode('_', $user->getRoles());

                    if (!str_contains($rolesString,'ADMIN')){
                        if (!in_array('ROLE_LINE_MANAGER', $user->getRoles())){
                            $qb->andWhere('e.id = :employeeId')->setParameter('employeeId', $user->getId());
                        }else{
                            $employeeIds=$userRepo->getEmployeesByLineManager($user);
                                $qb->andWhere('e.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
//                            $qb->andWhere("e.lineManager = :lineManager")->setParameter('lineManager', $user);
                        }
                    }else{
                        $userRole = [];
                        if (in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_POULTRY_USER');
                        }
                        if (in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_CATTLE_USER');
                        }
                        if (in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_AQUA_USER');
                        }
                        if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())){
                            array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
                        }
                        if($userRole){
                            $query = '';
                            foreach ($userRole as $key => $role) {
                                if ($key !== 0){
                                    $query .= " OR ";
                                }
                                $query .= "e.roles LIKE '%" . $role . "%'";

                            }
                            $qb->andWhere($query);
                        }

                    }

                    $qb->orderBy('e.name');
                    return $qb;
                },
                'choice_label' => function($employee){
                    /**  @var User $employee */
                return '(' . $employee->getUserId() . ') ' . $employee->getName();
                },
                'placeholder' => '- All Employee -',
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
                'placeholder' => '- Select month -',
                'required' => false,
                'data' => date('m')
            ])
            
            ->add('year', ChoiceType::class,[
                'choices' => $this->getYears(2020),
                'placeholder' => '- Select year -',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
                'data' => date('Y')
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
//        $years = range($min, ($max === 'current' ? date('Y') : $max));
        $years = range(($max === 'current' ? date('Y') : $max), $min);
        return array_combine($years, $years);
    }

    private function reportUserWise($user)
    {
        $otherReport = [];

        if (in_array('ROLE_CRM_SALES_MARKETING_USER', $user->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())) {
            $otherReport = array_merge(
                $otherReport,
                ['Sales & Marketing' =>
                    [
                        'DOC Price' => 'doc-price',
                        'Meat & Egg Price' => 'meat-egg-price',
                        'Feed Complain' => 'feed-complain',
                        'Problems' => 'challenges-problem',
                        'Ideas' => 'challenges-idea',
                        'Competitors Activity' => 'competitors-activity',
                    ]
                ]
            );
        }

        return $otherReport;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'loggedUser' => User::class,
            'userRepo' => UserRepository::class,
        ]);
    }
}