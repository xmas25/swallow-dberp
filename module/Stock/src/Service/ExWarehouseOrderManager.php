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
use Stock\Entity\ExWarehouseOrder;
use Store\Entity\Warehouse;

class ExWarehouseOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加其他出库
     * @param array $data
     * @param array $goodsData
     * @param $adminId
     * @return ExWarehouseOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addExWarehouseOrder(array $data, array $goodsData, $adminId)
    {
        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneBy(['warehouseId' => $data['warehouseId']]);

        $exWarehouseOrder = new ExWarehouseOrder();
        $exWarehouseOrder->valuesSet($data);
        $exWarehouseOrder->setExWarehouseOrderState(6);
        $exWarehouseOrder->setAdminId($adminId);
        $exWarehouseOrder->setExAddTime(time());
        $exWarehouseOrder->setOneWarehouse($warehouseInfo);

        $array = ['warehouseOrderGoodsAmount' => 0, 'warehouseOrderTax' => 0, 'warehouseOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['warehouseOrderGoodsAmount'] = $array['warehouseOrderGoodsAmount'] + $goodsData['goodsPrice'][$key] * $goodsData['goodsExNum'][$key];
            $array['warehouseOrderAmount']      = $array['warehouseOrderAmount'] + $goodsData['goodsAmount'][$key];
            $array['warehouseOrderTax']         = $array['warehouseOrderTax'] + $goodsData['goodsTax'][$key];
        }
        $exWarehouseOrder->setExWarehouseOrderGoodsAmount($array['warehouseOrderGoodsAmount']);
        $exWarehouseOrder->setExWarehouseOrderAmount($array['warehouseOrderAmount']);
        $exWarehouseOrder->setExWarehouseOrderTax($array['warehouseOrderTax']);

        $this->entityManager->persist($exWarehouseOrder);
        $this->entityManager->flush();

        return $exWarehouseOrder;
    }
}