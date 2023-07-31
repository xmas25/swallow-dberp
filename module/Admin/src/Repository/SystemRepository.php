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

namespace Admin\Repository;

use Admin\Entity\System;
use Doctrine\ORM\EntityRepository;

class SystemRepository extends EntityRepository
{
    public function findBaseSystem()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('s')
            ->from(System::class, 's')
            ->where($query->expr()->notIn('s.sysType', ['upload', 'customer']))
            ->orderBy('s.sysId', 'ASC');

        return $query->getQuery()->getResult();
    }
}