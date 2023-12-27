<?php

namespace Terminalbd\CrmBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Terminalbd\CrmBundle\Entity\ExpenseChartDetail;

class ExpenseChartDetailRepository extends EntityRepository
{

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function getExpenseChartDetailByExpenseChart($expenseChartId)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.amount', 'e.amount_type');
        $qb->addSelect('particular.id as particularId','particular.name as particularName');
        $qb->join('e.particular','particular');
        $qb->join('e.expenseChart','expenseChart');

        $qb->where('expenseChart.id =:expenseChart')->setParameter('expenseChart',$expenseChartId);

        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['particularId']]=$result;
            }
        }

        return $returnArray;
    }


}
