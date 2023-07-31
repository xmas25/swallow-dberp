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
use Stock\Entity\ExWarehouseOrder;

class ExWarehouseOrderCodeValidator extends AbstractValidator
{
    const NOT_SCALAR                        = 'notScalar';
    const EX_WAREHOUSE_ORDER_CODE_EXISTS    = 'exWarehouseOrderCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR                        => "这不是一个标准输入值",
        self::EX_WAREHOUSE_ORDER_CODE_EXISTS    => "该出库单号已经存在"
    ];

    private $entityManager;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
        }

        parent::__construct($options);
    }

    public function isValid($value): bool
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $warehouseOrderInfo = $this->entityManager->getRepository(ExWarehouseOrder::class)->findOneByExWarehouseOrderSn($value);

        if($warehouseOrderInfo != null) $isValid = false;
        else $isValid = true;

        if(!$isValid) $this->error(self::EX_WAREHOUSE_ORDER_CODE_EXISTS);

        return $isValid;
    }
}