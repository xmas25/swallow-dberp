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

namespace Stock\Validator;

use Laminas\Validator\AbstractValidator;
use Store\Entity\Warehouse;

class StockTransferWarehouseValidator extends AbstractValidator
{
    const NOT_SCALAR                = 'notScalar';
    const STOCK_WAREHOUSE_NOT_EXISTS= 'stockWarehouseNotExists';
    const STOCK_WAREHOUSE_NOT_SAME  = 'stockWarehouseNotSame';

    protected $messageTemplates = [
        self::NOT_SCALAR                => "这不是一个标准输入值",
        self::STOCK_WAREHOUSE_NOT_EXISTS=> "该仓库不存在",
        self::STOCK_WAREHOUSE_NOT_SAME  => "调出仓库和调入仓库不能相同"
    ];

    private $entityManager;
    private $inWarehouse;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
            if(isset($options['inWarehouse']))      $this->inWarehouse      = $options['inWarehouse'];
        }

        parent::__construct($options);
    }

    public function isValid($value, $context=null): bool
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneBy(['warehouseId' => $value]);
        if($warehouseInfo == null) $isValid = false;
        else $isValid = true;
        if(!$isValid) $this->error(self::STOCK_WAREHOUSE_NOT_EXISTS);

        if(!empty($this->inWarehouse)) {
            if($value == $context['transferOutWarehouseId']) {
                $isValid = false;
                $this->error(self::STOCK_WAREHOUSE_NOT_SAME);
            }
        }

        return $isValid;
    }
}