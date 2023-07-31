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

namespace Shop\Service;

use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrder;
use Shop\Entity\ShopOrderGoods;
use Store\Entity\Goods;

class ShopOrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加订单商品
     * @param array $data
     * @param int $shopOrderId
     * @param ShopOrder $shopOrder
     * @param $goodsBindType
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addShopOrderGoods(array $data, int $shopOrderId, ShopOrder $shopOrder, $goodsBindType): bool
    {

        foreach ($data as $value) {
            $shopOrderGoods = new ShopOrderGoods();
            $shopOrderGoods->setOrderGoodsId(null);
            $shopOrderGoods->setShopOrderId($shopOrderId);
            $shopOrderGoods->setGoodsName($value['goods_name']);
            $shopOrderGoods->setGoodsSpec($value['goods_spec']);
            $shopOrderGoods->setGoodsSn($value['goods_sn']);
            $shopOrderGoods->setGoodsUnitName($value['unit_name']);
            $shopOrderGoods->setGoodsPrice($value['goods_price']);
            $shopOrderGoods->setGoodsType($value['goods_type']);
            $shopOrderGoods->setBuyNum($value['buy_num']);
            $shopOrderGoods->setGoodsAmount($value['goods_amount']);
            $shopOrderGoods->setOneShopOrder($shopOrder);

            if (!empty($goodsBindType)) {
                $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy([$goodsBindType => $value['goods_sn']]);
                if ($goodsInfo) $shopOrderGoods->setDistributionState(4);
                else $shopOrderGoods->setDistributionState(3);
            } else $shopOrderGoods->setDistributionState(3);

            $this->entityManager->persist($shopOrderGoods);
            $this->entityManager->flush();
            $this->entityManager->clear(ShopOrderGoods::class);
        }

        return true;
    }

    /**
     * 订单商品修改匹配状态
     * @param $state
     * @param ShopOrderGoods $shopOrderGoods
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateShopOrderGoodsState($state, ShopOrderGoods $shopOrderGoods)
    {
        $shopOrderGoods->setDistributionState($state);
        $this->entityManager->flush();
    }

    /**
     * 订单商品修改 仓库信息和订单商品状态
     * @param $warehouseId
     * @param $warehouseName
     * @param $state
     * @param ShopOrderGoods $shopOrderGoods
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addShopOrderGoodsWarehouseAndState($warehouseId, $warehouseName, $state, ShopOrderGoods $shopOrderGoods)
    {
        $shopOrderGoods->setWarehouseId($warehouseId);
        $shopOrderGoods->setWarehouseName($warehouseName);
        $shopOrderGoods->setDistributionState($state);
        $this->entityManager->flush();
    }

    /**
     * 删除订单商品
     * @param int $shopOrderId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteShopOrderGoods(int $shopOrderId)
    {

        $orderGoods = $this->entityManager->getRepository(ShopOrderGoods::class)->findBy(['shopOrderId'=>$shopOrderId]);

        if($orderGoods) {
            foreach ($orderGoods as $goods) {
                $this->entityManager->remove($goods);
                $this->entityManager->flush();
            }
        }
        return true;
    }
}