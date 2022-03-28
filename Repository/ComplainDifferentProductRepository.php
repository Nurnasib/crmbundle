<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ComplainDifferentProductRepository extends EntityRepository
{
    public function getComplainByCreatedDateEmployeeReport($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('cdp')
                ->where('cdp.createdAt >= :startDate')
                ->andWhere('cdp.createdAt <= :endDate')
                ->andWhere('cdp.report = :report')
                ->andWhere('cdp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'report'=>$report, 'employee'=>$employee));
            $returnArray = [];

            foreach ($query->getQuery()->getResult() as $value){
                $createdMonth = $value->getCreatedAt()->format('F');

                $returnArray[$createdMonth][$value->getProductName()->getId()][]=$value;
            }
//            dd($returnArray);
            return $returnArray;
        }
        return array();
    }

}
