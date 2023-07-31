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
use Stock\Entity\OtherWarehouseOrderGoods;
use Store\Entity\Goods;

class OtherWarehouseOrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加其他入库商品
     * @param array $data
     * @param $warehouseId
     * @param $orderId
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addOtherWarehouseOrderGoods(array $data, $warehouseId, $orderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $value]);
            if($goodsInfo) {
                $otherWarehouseGoods = new OtherWarehouseOrderGoods();
                $otherWarehouseGoods->setWarehouseOrderGoodsId(null);
                $otherWarehouseGoods->setOtherWarehouseOrderId($orderId);
                $otherWarehouseGoods->setWarehouseId($warehouseId);
                $otherWarehouseGoods->setWarehouseGoodsBuyNum($data['goodsBuyNum'][$key]);
                $otherWarehouseGoods->setWarehouseGoodsPrice($data['goodsPrice'][$key]);
                $otherWarehouseGoods->setWarehouseGoodsTax($data['goodsTax'][$key]);
                $otherWarehouseGoods->setWarehouseGoodsAmount($data['goodsAmount'][$key]);
                $otherWarehouseGoods->setGoodsId($value);
                $otherWarehouseGoods->setGoodsName($goodsInfo->getGoodsName());
                $otherWarehouseGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $otherWarehouseGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $otherWarehouseGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());

                $this->entityManager->persist($otherWarehouseGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(OtherWarehouseOrderGoods::class);
            }
        }
    }
}