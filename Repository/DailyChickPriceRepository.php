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
use Doctrine\ORM\NonUniqueResultException;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class DailyChickPriceRepository extends EntityRepository
{

    public function getExistingReportByDateAndEmployee($employee)
    {
        $query = $this->createQueryBuilder('dcp')
            ->where('dcp.reportingDate = :reportingDate')
            ->andWhere('dcp.employee = :employee')
            ->setParameters(array('reportingDate' => (new \DateTime('now'))->format('Y-m-d'), 'employee' => $employee));
        return $query->getQuery()->getOneOrNullResult();

    }

}
