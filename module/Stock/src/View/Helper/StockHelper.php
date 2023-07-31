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

namespace Stock\View\Helper;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\I18n\Translator;

class StockHelper extends AbstractHelper
{
    private $entityManager;
    private $translator;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
    }

    /**
     * 库存盘点状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function stockCheckState($state, int $style = 1)
    {
        $checkState = Common::StockCheckState($this->translator, $style);

        return $checkState[$state];
    }

    /**
     * 库间调拨状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function stockTransferState($state, int $style = 1)
    {
        $transferState = Common::stockTransferState($this->translator, $style);

        return $transferState[$state];
    }

    /**
     * 库间调拨商品状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function transferGoodsState($state, int $style = 1)
    {
        $transferGoodsState = Common::transferGoodsState($this->translator, $style);

        return $transferGoodsState[$state];
    }
}