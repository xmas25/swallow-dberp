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
use Stock\Entity\ExWarehouseOrderGoods;
use Store\Entity\Goods;

class ExWarehouseOrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加其他出库商品
     * @param array $data
     * @param $warehouseId
     * @param $orderId
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addExWarehouseOrderGoods(array $data, $warehouseId, $orderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $value]);
            if ($goodsInfo) {
                $exWarehouseGoods = new ExWarehouseOrderGoods();
                $exWarehouseGoods->setExWarehouseOrderGoodsId(null);
                $exWarehouseGoods->setExWarehouseOrderId($orderId);
                $exWarehouseGoods->setWarehouseId($warehouseId);
                $exWarehouseGoods->setWarehouseGoodsExNum($data['goodsExNum'][$key]);
                $exWarehouseGoods->setWarehouseGoodsPrice($data['goodsPrice'][$key]);
                $exWarehouseGoods->setWarehouseGoodsTax($data['goodsTax'][$key]);
                $exWarehouseGoods->setWarehouseGoodsAmount($data['goodsAmount'][$key]);
                $exWarehouseGoods->setGoodsId($value);
                $exWarehouseGoods->setGoodsName($goodsInfo->getGoodsName());
                $exWarehouseGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $exWarehouseGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $exWarehouseGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());

                $this->entityManager->persist($exWarehouseGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(ExWarehouseOrderGoods::class);
            }
        }
    }
}