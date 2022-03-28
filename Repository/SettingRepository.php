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

    public function getProductTypeByParentParentChildren($childrenIds)
    {
        $qb = $this->createQueryBuilder('e');
            $qb->select('e.id','e.name')
        ->where("e.status =1")
        ->andWhere("e.settingType ='PRODUCT_TYPE'")
        ->andWhere("e.parent IN (:parent)")
        ->setParameter('parent',$childrenIds)
        ->orderBy('e.id', 'ASC');
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function getProductTypeWithOutChickByBreedName($childrenIds)
    {
        $qb = $this->createQueryBuilder('e');
            $qb->select('e.id','e.name')
        ->where("e.status =1")
        ->andWhere("e.settingType ='PRODUCT_TYPE'")
        ->andWhere("e.parent IN (:parent)")
        ->setParameter('parent',$childrenIds)
        ->andWhere("e.slug NOT IN (:slug)")
        ->setParameter('slug',['broiler-chicks','layer-chicks'])
        ->orderBy('e.id', 'ASC');
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function getProductTypeForBoilerChickByBreedName($childrenIds)
    {
        $qb = $this->createQueryBuilder('e');
            $qb->select('e.id','e.name')
        ->where("e.status =1")
        ->andWhere("e.settingType ='PRODUCT_TYPE'")
        ->andWhere("e.parent IN (:parent)")
        ->setParameter('parent',$childrenIds)
        ->andWhere("e.slug IN (:slug)")
        ->setParameter('slug',['broiler-chicks'])
        ->orderBy('e.id', 'ASC');
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function getProductTypeForLayerChickByBreedName($childrenIds)
    {
        $qb = $this->createQueryBuilder('e');
            $qb->select('e.id','e.name')
        ->where("e.status =1")
        ->andWhere("e.settingType ='PRODUCT_TYPE'")
        ->andWhere("e.parent IN (:parent)")
        ->setParameter('parent',$childrenIds)
        ->andWhere("e.slug IN (:slug)")
        ->setParameter('slug',['layer-chicks'])
        ->orderBy('e.id', 'ASC');
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function getFarmerPurposeByServiceMode($serviceMode){
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.status =1");
        $qb->andWhere("e.settingType ='PURPOSE'");
        if($serviceMode=='sales-marketing'||$serviceMode=='sales-service-marketing'){
          $qb->andWhere("e.slug IN (:slug)")->setParameter('slug',['pond-included','cattle-farm-included','layer-shed-included','broiler-shed-included']);
        }else{
            $qb->andWhere("e.slug NOT IN (:slug) OR e.slug IS NULL")->setParameter('slug',['pond-included','cattle-farm-included','layer-shed-included','broiler-shed-included']);
        }
        $qb->orderBy('e.name', 'ASC');
        $results = $qb->getQuery()->getResult();
        return $results;
    }


    public function getProductType($breed)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.parent', 'parent');

        $qb->select('e.id', 'e.name');
        $qb->addSelect('parent.name AS parentName');

        $qb->where("e.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('parent.parent = :breed')->setParameter('breed', $breed);
        $qb->andWhere('e.status = 1');
        $qb->andWhere("e.slug NOT IN (:slug)");
        $qb->setParameter('slug',['broiler-chicks','layer-chicks']);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['parentName']][] = [
                'id' => $result['id'],
                'name' => $result['name'],
                'parent' => $result['parentName'],
            ];
        }

        return $data;
    }

    public function getProductTypeForBoilerChick($breed)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.parent', 'parent');

        $qb->select('e.id', 'e.name');
        $qb->addSelect('parent.name AS parentName');

        $qb->where("e.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('parent.parent = :breed')->setParameter('breed', $breed);
        $qb->andWhere('e.status = 1');
        $qb->andWhere("e.slug IN (:slug)");
        $qb->setParameter('slug',['broiler-chicks']);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['parentName']][] = [
                'id' => $result['id'],
                'name' => $result['name'],
                'parent' => $result['parentName'],
            ];
        }

        return $data;
    }

    public function getProductTypeForLayerChick($breed)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.parent', 'parent');

        $qb->select('e.id', 'e.name');
        $qb->addSelect('parent.name AS parentName');

        $qb->where("e.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('parent.parent = :breed')->setParameter('breed', $breed);
        $qb->andWhere('e.status = 1');
        $qb->andWhere("e.slug IN (:slug)");
        $qb->setParameter('slug',['layer-chicks']);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['parentName']][] = [
                'id' => $result['id'],
                'name' => $result['name'],
                'parent' => $result['parentName'],
            ];
        }

        return $data;
    }

}
