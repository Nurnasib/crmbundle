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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitPlan;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CrmTourPlanSearchFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['loggedUser'];
        $userRepo = $options['userRepo'];
        $builder

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
                'placeholder' => '- Select Employee -',
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add($builder->create('visitDate', TextType::class, array(
                'label' => 'Date',
                'required' => true,
//                'mapped' => false,
                'attr' => array(
                    'class' => 'visit_date monthYearPicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'm-Y'
                ),
                'empty_data' => new \DateTime(),
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'm-Y')))
            ->add('Save', SubmitType::class)
        ;
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