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
class SettingRepository extends EntityRepository
{

    public function getReportByParentSlug($slug){
        $return = array();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.parent','parent');
        $qb->where('parent.slug = :slug')->setParameter('slug',$slug);
        $qb->andWhere('e.settingType = :type')->setParameter('type','FARMER_REPORT');
        $result = $qb->getQuery()->getResult();
        /*for($i=1; $i<=$result[0]['numberOfWeek'];$i++){
            $return[$i]= $i.' week';
        }*/
        return $result;
    }

    public function getReportByParentWithoutAfterFcr($parentId){
        $return = array();
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.parent = :parentId')->setParameter('parentId',$parentId);
        $qb->andWhere('e.settingType = :type')->setParameter('type','FARMER_REPORT');
        $qb->andWhere('e.settingType = :type')->setParameter('type','FARMER_REPORT');
        $result = $qb->getQuery()->getResult();
        /*for($i=1; $i<=$result[0]['numberOfWeek'];$i++){
            $return[$i]= $i.' week';
        }*/
        return $result;
    }

}
