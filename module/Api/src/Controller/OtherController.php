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

namespace Api\Controller;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Shop\Service\ShopOrderDeliveryAddressManager;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;

class OtherController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $shopOrderManager;
    private $shopOrderGoodsManager;
    private $shopOrderDeliveryAddressManager;

    private $dataArray;
    private $appId;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        ShopOrderManager $shopOrderManager,
        ShopOrderGoodsManager $shopOrderGoodsManager,
        ShopOrderDeliveryAddressManager $shopOrderDeliveryAddressManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->shopOrderManager = $shopOrderManager;
        $this->shopOrderGoodsManager = $shopOrderGoodsManager;
        $this->shopOrderDeliveryAddressManager = $shopOrderDeliveryAddressManager;
    }


}