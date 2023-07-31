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
use Stock\Entity\StockTransfer;

class StockTransferRepository extends EntityRepository
{
    /**
     * 库间调拨列表
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findStockTransferList(array $search = []): \Doctrine\ORM\Query
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('t', 'o', 'i')
            ->from(StockTransfer::class, 't')
            ->leftJoin('t.outOneWarehouse', 'o')
            ->leftJoin('t.inOneWarehouse', 'i')
            ->orderBy('t.transferState', 'ASC')
            ->addOrderBy('t.transferId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder;
    }
}