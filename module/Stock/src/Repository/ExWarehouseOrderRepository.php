<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Stock\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Stock\Entity\ExWarehouseOrder;

class ExWarehouseOrderRepository extends EntityRepository
{
    /**
     * 其他出库订单列表
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findExWarehouseOrderList(array $search = []): \Doctrine\ORM\Query
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('e', 'w')
            ->from(ExWarehouseOrder::class, 'e')
            ->join('e.oneWarehouse', 'w')
            ->orderBy('e.exWarehouseOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder): QueryBuilder
    {
        if(isset($search['warehouse_order_sn']) && !empty($search['warehouse_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('e.exWarehouseOrderSn', "'%".$search['warehouse_order_sn']."%'"));
        if(isset($search['start_amount']) && $search['start_amount'] > 0)                       $queryBuilder->andWhere($queryBuilder->expr()->gte('e.exWarehouseOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                           $queryBuilder->andWhere($queryBuilder->expr()->lte('e.exWarehouseOrderAmount', $search['end_amount']));
        if(isset($search['start_time']) && !empty($search['start_time']))                       $queryBuilder->andWhere($queryBuilder->expr()->gte('e.exAddTime', ':startTime'))->setParameter('startTime', strtotime($search['start_time']));
        if(isset($search['end_time']) && !empty($search['end_time']))                           $queryBuilder->andWhere($queryBuilder->expr()->lte('e.exAddTime', ':endTime'))->setParameter('endTime', strtotime($search['end_time']));
        if(isset($search['warehouse_id']) && $search['warehouse_id'] > 0)                       $queryBuilder->andWhere($queryBuilder->expr()->eq('e.warehouseId', $search['warehouse_id']));
        if(isset($search['warehouse_order_info']) && !empty($search['warehouse_order_info']))   $queryBuilder->andWhere($queryBuilder->expr()->like('e.exWarehouseOrderInfo', "'%".$search['warehouse_order_info']."%'"));

        return $queryBuilder;
    }
}