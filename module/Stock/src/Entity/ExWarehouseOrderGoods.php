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
 * 其他出库商品
 * @ORM\Entity(repositoryClass="Stock\Repository\ExWarehouseOrderGoodsRepository")
 * @ORM\Table(name="dberp_ex_warehouse_order_goods")
 */
class ExWarehouseOrderGoods extends BaseEntity
{
    /**
     * 出库商品id
     * @ORM\Id()
     * @ORM\Column(name="ex_warehouse_order_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $exWarehouseOrderGoodsId;

    /**
     * 出库对应表id
     * @ORM\Column(name="ex_warehouse_order_id", type="integer", length=11)
     */
    private $exWarehouseOrderId;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 出库数量
     * @ORM\Column(name="warehouse_goods_ex_num", type="integer", length=11)
     */
    private $warehouseGoodsExNum;

    /**
     * 商品价格
     * @ORM\Column(name="warehouse_goods_price", type="decimal", scale=4)
     */
    private $warehouseGoodsPrice;

    /**
     * 商品税金
     * @ORM\Column(name="warehouse_goods_tax", type="decimal", scale=4)
     */
    private $warehouseGoodsTax;

    /**
     * 商品总金额
     * @ORM\Column(name="warehouse_goods_amount", type="decimal", scale=4)
     */
    private $warehouseGoodsAmount;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 商品名称
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * 商品编号
     * @ORM\Column(name="goods_number", type="string", length=30)
     */
    private $goodsNumber;

    /**
     * 商品规格
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * 商品单位
     * @ORM\Column(name="goods_unit", type="string", length=20)
     */
    private $goodsUnit;

    /**
     * @return mixed
     */
    public function getExWarehouseOrderGoodsId()
    {
        return $this->exWarehouseOrderGoodsId;
    }

    /**
     * @param mixed $exWarehouseOrderGoodsId
     */
    public function setExWarehouseOrderGoodsId($exWarehouseOrderGoodsId): void
    {
        $this->exWarehouseOrderGoodsId = $exWarehouseOrderGoodsId;
    }

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
    public function getWarehouseGoodsExNum()
    {
        return $this->warehouseGoodsExNum;
    }

    /**
     * @param mixed $warehouseGoodsExNum
     */
    public function setWarehouseGoodsExNum($warehouseGoodsExNum): void
    {
        $this->warehouseGoodsExNum = $warehouseGoodsExNum;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsPrice()
    {
        return $this->warehouseGoodsPrice;
    }

    /**
     * @param mixed $warehouseGoodsPrice
     */
    public function setWarehouseGoodsPrice($warehouseGoodsPrice): void
    {
        $this->warehouseGoodsPrice = $warehouseGoodsPrice;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsTax()
    {
        return $this->warehouseGoodsTax;
    }

    /**
     * @param mixed $warehouseGoodsTax
     */
    public function setWarehouseGoodsTax($warehouseGoodsTax): void
    {
        $this->warehouseGoodsTax = $warehouseGoodsTax;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsAmount()
    {
        return $this->warehouseGoodsAmount;
    }

    /**
     * @param mixed $warehouseGoodsAmount
     */
    public function setWarehouseGoodsAmount($warehouseGoodsAmount): void
    {
        $this->warehouseGoodsAmount = $warehouseGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param mixed $goodsId
     */
    public function setGoodsId($goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return mixed
     */
    public function getGoodsName()
    {
        return $this->goodsName;
    }

    /**
     * @param mixed $goodsName
     */
    public function setGoodsName($goodsName): void
    {
        $this->goodsName = $goodsName;
    }

    /**
     * @return mixed
     */
    public function getGoodsNumber()
    {
        return $this->goodsNumber;
    }

    /**
     * @param mixed $goodsNumber
     */
    public function setGoodsNumber($goodsNumber): void
    {
        $this->goodsNumber = $goodsNumber;
    }

    /**
     * @return mixed
     */
    public function getGoodsSpec()
    {
        return $this->goodsSpec;
    }

    /**
     * @param mixed $goodsSpec
     */
    public function setGoodsSpec($goodsSpec): void
    {
        $this->goodsSpec = $goodsSpec;
    }

    /**
     * @return mixed
     */
    public function getGoodsUnit()
    {
        return $this->goodsUnit;
    }

    /**
     * @param mixed $goodsUnit
     */
    public function setGoodsUnit($goodsUnit): void
    {
        $this->goodsUnit = $goodsUnit;
    }
}