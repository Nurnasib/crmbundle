<?php
namespace Terminalbd\CrmBundle\Repository;
use Terminalbd\CrmBundle\Entity\AppVersions;
use Doctrine\ORM\EntityRepository;
use function Doctrine\ORM\QueryBuilder;
use function Symfony\Component\String\s;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class AppVersionRepository extends EntityRepository
{
    public function getActiveAppVersion()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.version', 'e.mode', 'e.status', 'e.contents');
        $qb->where('e.status =:status')->setParameter('status', 1);
        $qb->orderBy('e.id', 'DESC');
        $qb->setMaxResults(1);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }

}
