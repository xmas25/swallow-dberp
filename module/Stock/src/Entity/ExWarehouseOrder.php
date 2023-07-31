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

namespace Stock\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 其他出库
 * @ORM\Entity(repositoryClass="Stock\Repository\ExWarehouseOrderRepository")
 * @ORM\Table(name="dberp_ex_warehouse_order")
 */
class ExWarehouseOrder extends BaseEntity
{
    /**
     * 出库单id
     * @ORM\Id()
     * @ORM\Column(name="ex_warehouse_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $exWarehouseOrderId;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 出库单号
     * @ORM\Column(name="ex_warehouse_order_sn", type="string", length=50)
     */
    private $exWarehouseOrderSn;

    /**
     * 出库单状态：6 已出库
     * @ORM\Column(name="ex_warehouse_order_state", type="integer", length=1)
     */
    private $exWarehouseOrderState;

    /**
     * 出库单备注信息
     * @ORM\Column(name="ex_warehouse_order_info", type="string", length=255)
     */
    private $exWarehouseOrderInfo;

    /**
     * 出库单商品总价
     * @ORM\Column(name="ex_warehouse_order_goods_amount", type="decimal", scale=4)
     */
    private $exWarehouseOrderGoodsAmount;

    /**
     * 出库单税金
     * @ORM\Column(name="ex_warehouse_order_tax", type="decimal", scale=4)
     */
    private $exWarehouseOrderTax;

    /**
     * 出库单总金额
     * @ORM\Column(name="ex_warehouse_order_amount", type="decimal", scale=4)
     */
    private $exWarehouseOrderAmount;

    /**
     * 出库时间
     * @ORM\Column(name="ex_add_time", type="integer", length=10)
     */
    private $exAddTime;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $oneWarehouse;

    /**
     * @return mixed
     */
    public function getExWarehouseOrderId()
    {
        return $this->exWarehouseOrderId;
    }

    /**
     * @param mixed $exWarehouseOrderId
     */
    public function setExWarehouseOrderId($exWarehouseOrderId): void
    {
        $this->exWarehouseOrderId = $exWarehouseOrderId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId): void
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderSn()
    {
        return $this->exWarehouseOrderSn;
    }

    /**
     * @param mixed $exWarehouseOrderSn
     */
    public function setExWarehouseOrderSn($exWarehouseOrderSn): void
    {
        $this->exWarehouseOrderSn = $exWarehouseOrderSn;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderState()
    {
        return $this->exWarehouseOrderState;
    }

    /**
     * @param mixed $exWarehouseOrderState
     */
    public function setExWarehouseOrderState($exWarehouseOrderState): void
    {
        $this->exWarehouseOrderState = $exWarehouseOrderState;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderInfo()
    {
        return $this->exWarehouseOrderInfo;
    }

    /**
     * @param mixed $exWarehouseOrderInfo
     */
    public function setExWarehouseOrderInfo($exWarehouseOrderInfo): void
    {
        $this->exWarehouseOrderInfo = $exWarehouseOrderInfo;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderGoodsAmount()
    {
        return $this->exWarehouseOrderGoodsAmount;
    }

    /**
     * @param mixed $exWarehouseOrderGoodsAmount
     */
    public function setExWarehouseOrderGoodsAmount($exWarehouseOrderGoodsAmount): void
    {
        $this->exWarehouseOrderGoodsAmount = $exWarehouseOrderGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderTax()
    {
        return $this->exWarehouseOrderTax;
    }

    /**
     * @param mixed $exWarehouseOrderTax
     */
    public function setExWarehouseOrderTax($exWarehouseOrderTax): void
    {
        $this->exWarehouseOrderTax = $exWarehouseOrderTax;
    }

    /**
     * @return mixed
     */
    public function getExWarehouseOrderAmount()
    {
        return $this->exWarehouseOrderAmount;
    }

    /**
     * @param mixed $exWarehouseOrderAmount
     */
    public function setExWarehouseOrderAmount($exWarehouseOrderAmount): void
    {
        $this->exWarehouseOrderAmount = $exWarehouseOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getExAddTime()
    {
        return $this->exAddTime;
    }

    /**
     * @param mixed $exAddTime
     */
    public function setExAddTime($exAddTime): void
    {
        $this->exAddTime = $exAddTime;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getOneWarehouse()
    {
        return $this->oneWarehouse;
    }

    /**
     * @param mixed $oneWarehouse
     */
    public function setOneWarehouse($oneWarehouse): void
    {
        $this->oneWarehouse = $oneWarehouse;
    }
}