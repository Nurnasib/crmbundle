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

use App\Entity\Admin\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class NourishLocationRepository extends MaterializedPathRepository{


    public function getFlatTree()
    {

        $locations = $this->childrenHierarchy();

        $this->buildFlatTree($locations, $array);

        return $array;
    }

    public function getFlatLocationTree()
    {

        $locations = $this->childrenHierarchy();

        $this->buildFlatLocationTree($locations, $array);

        return $array;
    }

    private function buildFlatTree($locations, &$array = array())
    {
        usort($locations, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($locations as $location) {
            $array[$location['id']] = $this->formatLabel($location['level'], $location['name']);
            if(isset($location['__children'])) {
                $this->buildFlatTree($location['__children'], $array);
            }
        }
    }

    private function buildFlatLocationTree($locations, &$array = array())
    {
        usort($locations, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($locations as $location) {
            $array[] = $this->find($location['id']);
            if(isset($location['__children'])) {
                $this->buildFlatLocationTree($location['__children'], $array);
            }
        }
    }

    private function formatLabel($level, $value) {
        $level = $level - 1;
        return str_repeat("-", $level * 5) . str_repeat(">", $level) . "$value";
    }


    public function getLocationOptions(){

        $ret = array();
        $em = $this->_em;
        $locations = $this->findBy(array(),array('parent'=>'asc'));

        foreach( $locations as $cat ){
            if( !$cat->getParent() ){
                continue;
            }
            if(!array_key_exists($cat->getParent()->getName(), $ret) ){
                $ret[ $cat->getParent()->getName() ] = array();
            }
            $ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
        }
        return $ret;
    }

    /**
     * @param $locations location[]
     * @return array
     */
    public function buildLocationGroup($locations)
    {
        $result = array();

        foreach($locations as $location) {
            $parentLocation = $this->getParentLocationByLevel($location, 3);


            if(empty($parentLocation)) {
                continue;
            }

            $parentId = $parentLocation->getId();

            if(!isset($result[$parentId])) {
                $result[$parentId] = array(
                    'name' =>  $parentLocation->getName(),
                    'slug' =>  $parentLocation->getSlug(),
                    '__children' =>  array(),
                );
            }

            $result[$parentId]['__children'][] = array(
                'name' => $location->getName(),
                'slug' => $location->getSlug()
            );
        }

        return $result;
    }

    public function getLocationOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.parent', 'ASC')
            ->where('node.level < 6')
            ->getQuery()
            ->getResult();

        $locations = $this->getLocationsIndexedById($results);

        $grouped = array();

        foreach ($locations as $location) {
            switch($location->getLevel()) {

                case 4:
                    $grouped[$locations[$location->getParentIdByLevel(4)]->getName()][$location->getId()] = $location;
            }
        }

        return $grouped;
    }

    public function getUpozilaOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.parent', 'ASC')
            ->where('node.level < 6')
            ->getQuery()
            ->getResult();

        $locations = $this->getLocationsIndexedById($results);

        $grouped = array();

        foreach ($locations as $location) {
            switch($location->getLevel()) {

                case 5:
                    $grouped[$locations[$location->getParentIdByLevel(4)]->getName()][$location->getId()] = $location;
            }
        }

        return $grouped;
    }

    public function getDistrictOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.level, node.name', 'ASC')
            ->where('node.level < 3')
            ->getQuery()
            ->getResult();

        $locations = $this->getLocationsIndexedById($results);

        $grouped = array();

        foreach ($locations as $location) {
            switch($location->getLevel()) {
                case 2:
                    $grouped[$locations[$location->getParentIdByLevel(2)]->getName()][$location->getId()] = $location;
            }
        }

        return $grouped;
    }

    /**
     * @param Location $location
     * @param int $level
     * @return Location
     */
    public function getParentLocationByLevel(Location $location, $level = 4)
    {
        return $this->find($location->getParentIdByLevel($level));
    }

    /**
     * @param $results
     * @return Location[]
     */
    protected function getLocationsIndexedById($results)
    {
        $locations = array();

        foreach ($results as $location) {
            $locations[$location->getId()] = $location;
        }
        return $locations;
    }


    public function getUnderChild($parent,$thana)
    {

        $em = $this->_em;
        $entities = $em->getRepository('SettingLocationBundle:Location')->findBy(array('parent'=>$parent),array('name'=>'asc'));
        $data = '';
        $data .= '<label>Thana/Upazilla</label>';
        $data .= '<p><select id="thana" name="thana" class="select2"  >';
        foreach( $entities as $entity){
            if ($thana === $entity->getId()){ $selected = 'selected' ; }else{ $selected=''; }
            $data .='<option  '.$selected.'  value="'. $entity->getId() .'">'.$entity->getName().'</option>';
        }
        $data .='</select></p>';
        return $data;

    }

    public function searchAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.parent','parent');
        $query->select('e.id as id');
        $query->addSelect('CONCAT(e.name, \',\', parent.name) AS text');
        $query->where("e.level = 3");
        $query->andWhere($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function apiInsert($data)
    {
        $em = $this->_em;
        foreach ($data as $key => $value){
            $entity = new Location();
            $entity->setOldId($data[$key]['id']);
            $entity->setName($data[$key]['name']);
            if($data[$key]['parent']){
                $parent = $this->findOneBy(array('oldId' => $data[$key]['parent']));
                $entity->setParent($parent);
            }
            $em->persist($entity);
            $em->flush();
        }
    }

    public function parentLevel()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.id');
        $result = $qb->getQuery()->getResult();
        return $result;
    }


    public function totalCount()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.id) as count');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * Agent
     */
    public function findBySearchQuery($parameter , $data ): array
    {


        if (!empty($parameter['orderBy'])) {
            $sortBy = $parameter['orderBy'];
            $order = $parameter['order'];
        }

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.parent','p');
        $qb->select('e.id as id','e.name as name','p.name as district');
        $qb->setFirstResult($parameter['offset']);
        $qb->setMaxResults($parameter['limit']);
        if ($parameter['orderBy']){
            $qb->orderBy($sortBy, $order);
        }else{
            $qb->orderBy('e.id', 'DESC');
        }
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function getGroupUpozila()
    {

    }



}
