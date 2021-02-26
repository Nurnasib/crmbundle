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
class AgentUpgradationReportRepository extends BaseRepository
{
    public function getAgentUpgradationReportByCreatedDateEmployeeReport($purpose, $employee){
        if($purpose&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('aur')
                ->where('aur.createdAt >= :startDate')
                ->andWhere('aur.createdAt <= :endDate')
                ->andWhere('aur.agentPurpose = :purpose')
                ->andWhere('aur.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'purpose'=>$purpose, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }


}
