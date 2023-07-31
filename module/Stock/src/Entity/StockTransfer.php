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
 * 库间调拨
 * @ORM\Entity(repositoryClass="Stock\Repository\StockTransferRepository")
 * @ORM\Table(name="dberp_stock_transfer")
 */
class StockTransfer extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="transfer_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $transferId;

    /**
     * 调拨编号
     * @ORM\Column(name="transfer_sn", type="string", length=50)
     */
    private $transferSn;

    /**
     * 出库id
     * @ORM\Column(name="transfer_in_warehouse_id", type="integer", length=11)
     */
    private $transferInWarehouseId;

    /**
     * 入库id
     * @ORM\Column(name="transfer_out_warehouse_id", type="integer", length=11)
     */
    private $transferOutWarehouseId;

    /**
     * 添加时间
     * @ORM\Column(name="transfer_add_time", type="integer", length=10)
     */
    private $transferAddTime;

    /**
     * 完成时间
     * @ORM\Column(name="transfer_finish_time", type="integer", length=10)
     */
    private $transferFinishTime;

    /**
     * 调拨备注
     * @ORM\Column(name="transfer_info", type="string", length=500)
     */
    private $transferInfo;

    /**
     * 调拨状态，0 待调拨，1 已调拨，2 部分已调拨
     * @ORM\Column(name="transfer_state", type="integer", length=1)
     */
    private $transferState;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="transfer_out_warehouse_id", referencedColumnName="warehouse_id")
     */
    private $outOneWarehouse;

    /**
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="transfer_in_warehouse_id", referencedColumnName="warehouse_id")
     */
    private $inOneWarehouse;

    /**
     * @return mixed
     */
    public function getOutOneWarehouse()
    {
        return $this->outOneWarehouse;
    }

    /**
     * @param mixed $outOneWarehouse
     */
    public function setOutOneWarehouse($outOneWarehouse)
    {
        $this->outOneWarehouse = $outOneWarehouse;
    }

    /**
     * @return mixed
     */
    public function getInOneWarehouse()
    {
        return $this->inOneWarehouse;
    }

    /**
     * @param mixed $inOneWarehouse
     */
    public function setInOneWarehouse($inOneWarehouse)
    {
        $this->inOneWarehouse = $inOneWarehouse;
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
    public function getTransferSn()
    {
        return $this->transferSn;
    }

    /**
     * @param mixed $transferSn
     */
    public function setTransferSn($transferSn): void
    {
        $this->transferSn = $transferSn;
    }

    /**
     * @return mixed
     */
    public function getTransferInWarehouseId()
    {
        return $this->transferInWarehouseId;
    }

    /**
     * @param mixed $transferInWarehouseId
     */
    public function setTransferInWarehouseId($transferInWarehouseId): void
    {
        $this->transferInWarehouseId = $transferInWarehouseId;
    }

    /**
     * @return mixed
     */
    public function getTransferOutWarehouseId()
    {
        return $this->transferOutWarehouseId;
    }

    /**
     * @param mixed $transferOutWarehouseId
     */
    public function setTransferOutWarehouseId($transferOutWarehouseId): void
    {
        $this->transferOutWarehouseId = $transferOutWarehouseId;
    }

    /**
     * @return mixed
     */
    public function getTransferAddTime()
    {
        return $this->transferAddTime;
    }

    /**
     * @param mixed $transferAddTime
     */
    public function setTransferAddTime($transferAddTime): void
    {
        $this->transferAddTime = $transferAddTime;
    }

    /**
     * @return mixed
     */
    public function getTransferFinishTime()
    {
        return $this->transferFinishTime;
    }

    /**
     * @param mixed $transferFinishTime
     */
    public function setTransferFinishTime($transferFinishTime): void
    {
        $this->transferFinishTime = $transferFinishTime;
    }

    /**
     * @return mixed
     */
    public function getTransferInfo()
    {
        return $this->transferInfo;
    }

    /**
     * @param mixed $transferInfo
     */
    public function setTransferInfo($transferInfo): void
    {
        $this->transferInfo = $transferInfo;
    }

    /**
     * @return mixed
     */
    public function getTransferState()
    {
        return $this->transferState;
    }

    /**
     * @param mixed $transferState
     */
    public function setTransferState($transferState): void
    {
        $this->transferState = $transferState;
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
}