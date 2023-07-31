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
use Stock\Entity\StockTransferGoods;
use Store\Entity\Goods;

class StockTransferGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 商品调拨
     * @param array $data
     * @param $outWarehouseId
     * @param $inWarehouseId
     * @param $transferId
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addStockTransferGoods(array $data, $outWarehouseId, $inWarehouseId, $transferId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $value]);
            if($goodsInfo) {
                $stockTransferGoods = new StockTransferGoods();
                $stockTransferGoods->setTransferId($transferId);
                $stockTransferGoods->setOutWarehouseId($outWarehouseId);
                $stockTransferGoods->setInWarehouseId($inWarehouseId);
                $stockTransferGoods->setTransferGoodsNum($data['transferGoodsNum'][$key]);
                $stockTransferGoods->setTransferGoodsState(0);
                $stockTransferGoods->setGoodsId($value);
                $stockTransferGoods->setGoodsName($goodsInfo->getGoodsName());
                $stockTransferGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $stockTransferGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $stockTransferGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());

                $this->entityManager->persist($stockTransferGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(StockTransferGoods::class);
            }
        }
    }

    /**
     * 更新库间调拨中的商品状态，0 未调拨，1 已调拨，2 库存不足，未调拨
     * @param $state
     * @param StockTransferGoods $stockTransferGoods
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStockTransferGoodsState($state, StockTransferGoods $stockTransferGoods)
    {
        $stockTransferGoods->setTransferGoodsState($state);
        $this->entityManager->flush();
    }

    /**
     * 删除库间调拨商品
     * @param $transferId
     */
    public function deleteStockTransferIdGoods($transferId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(StockTransferGoods::class, 't')
            ->where('t.transferId = :transferId')->setParameter('transferId', $transferId);

        $qb->getQuery()->execute();
    }
}