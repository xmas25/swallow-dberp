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
 * 库间商品调拨
 * @ORM\Entity(repositoryClass="Stock\Repository\StockTransferGoodsRepository")
 * @ORM\Table(name="dberp_stock_transfer_goods")
 */
class StockTransferGoods extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="transfer_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $transferGoodsId;

    /**
     * 调拨id
     * @ORM\Column(name="transfer_id", type="integer", length=11)
     */
    private $transferId;

    /**
     * 调入仓库id
     * @ORM\Column(name="in_warehouse_id", type="integer", length=11)
     */
    private $inWarehouseId;

    /**
     * 调出仓库id
     * @ORM\Column(name="out_warehouse_id", type="integer", length=11)
     */
    private $outWarehouseId;

    /**
     * 商品调拨数量
     * @ORM\Column(name="transfer_goods_num", type="integer", length=11)
     */
    private $transferGoodsNum;

    /**
     * 0 未调拨，1 已调拨，2 库存不足，未调拨
     * @ORM\Column(name="transfer_goods_state", type="integer", length=1)
     */
    private $transferGoodsState;

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
    public function getTransferGoodsId()
    {
        return $this->transferGoodsId;
    }

    /**
     * @param mixed $transferGoodsId
     */
    public function setTransferGoodsId($transferGoodsId): void
    {
        $this->transferGoodsId = $transferGoodsId;
    }

    /**
     * @return mixed
     */
    public function getTransferId()
    {
        return $this->transferId;
    }

    /**
     * @param mixed $transferId
     */
    public function setTransferId($transferId): void
    {
        $this->transferId = $transferId;
    }

    /**
     * @return mixed
     */
    public function getInWarehouseId()
    {
        return $this->inWarehouseId;
    }

    /**
     * @param mixed $inWarehouseId
     */
    public function setInWarehouseId($inWarehouseId): void
    {
        $this->inWarehouseId = $inWarehouseId;
    }

    /**
     * @return mixed
     */
    public function getOutWarehouseId()
    {
        return $this->outWarehouseId;
    }

    /**
     * @param mixed $outWarehouseId
     */
    public function setOutWarehouseId($outWarehouseId): void
    {
        $this->outWarehouseId = $outWarehouseId;
    }

    /**
     * @return mixed
     */
    public function getTransferGoodsNum()
    {
        return $this->transferGoodsNum;
    }

    /**
     * @param mixed $transferGoodsNum
     */
    public function setTransferGoodsNum($transferGoodsNum): void
    {
        $this->transferGoodsNum = $transferGoodsNum;
    }

    /**
     * @return mixed
     */
    public function getTransferGoodsState()
    {
        return $this->transferGoodsState;
    }

    /**
     * @param mixed $transferGoodsState
     */
    public function setTransferGoodsState($transferGoodsState): void
    {
        $this->transferGoodsState = $transferGoodsState;
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