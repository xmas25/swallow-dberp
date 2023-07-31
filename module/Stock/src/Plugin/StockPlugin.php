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

namespace Stock\Plugin;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\I18n\Translator;
use Laminas\Session\Container;

class StockPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;

    private $adminSession;

    public function __construct(
        EntityManager $entityManager,
        Translator $translator
    )
    {
        $this->entityManager= $entityManager;
        $this->translator   = $translator;

        $this->adminSession = new Container('admin');
    }

    /**
     * 其他入库单号生成
     * @param string $prefix
     * @return string
     */
    public function createOtherWarehouseOrderSn(string $prefix = 'O'): string
    {
        $adminId = $this->adminSession->offsetGet('admin_id');

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }

    /**
     * 其他出库单号生成
     * @param string $prefix
     * @return string
     */
    public function createExWarehouseOrderSn(string $prefix = 'E'): string
    {
        $adminId = $this->adminSession->offsetGet('admin_id');

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }

    /**
     * 盘点单号生成
     * @param string $prefix
     * @return string
     */
    public function createStockCheckOrderSn(string $prefix = 'N'): string
    {
        $adminId = $this->adminSession->offsetGet('admin_id');

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }

    /**
     * 调拨单号生成
     * @param string $prefix
     * @return string
     */
    public function createStockTransferSn(string $prefix = 'D'): string
    {
        $adminId = $this->adminSession->offsetGet('admin_id');

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }
}