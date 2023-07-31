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

namespace Stock\Service;

use Doctrine\ORM\EntityManager;
use Stock\Entity\StockTransfer;
use Store\Entity\Warehouse;

class StockTransferManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加库间调拨
     * @param array $data
     * @param $adminId
     * @return StockTransfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addStockTransfer(array $data, $adminId): StockTransfer
    {
        $outWarehouse   = $this->entityManager->getRepository(Warehouse::class)->findOneBy(['warehouseId' => $data['transferOutWarehouseId']]);
        $inWarehouse    = $this->entityManager->getRepository(Warehouse::class)->findOneBy(['warehouseId' => $data['transferInWarehouseId']]);

        $stockTransfer = new StockTransfer();
        $stockTransfer->valuesSet($data);
        $stockTransfer->setTransferState(0);
        $stockTransfer->setAdminId($adminId);
        $stockTransfer->setTransferAddTime(strtotime($data['transferAddTime']));
        $stockTransfer->setOutOneWarehouse($outWarehouse);
        $stockTransfer->setInOneWarehouse($inWarehouse);

        $this->entityManager->persist($stockTransfer);
        $this->entityManager->flush();

        return $stockTransfer;
    }

    /**
     * 更新库间调拨状态
     * @param $state
     * @param $finishTime
     * @param StockTransfer $stockTransfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStockTransferState($state, $finishTime, StockTransfer $stockTransfer)
    {
        $stockTransfer->setTransferState($state);
        $stockTransfer->setTransferFinishTime($finishTime);

        $this->entityManager->flush();
    }

    /**
     * 删除库间调拨
     * @param StockTransfer $stockTransfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteStockTransfer(StockTransfer $stockTransfer)
    {
        $this->entityManager->remove($stockTransfer);
        $this->entityManager->flush();
    }
}