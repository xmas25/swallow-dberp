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
use Stock\Entity\StockTransfer;

class StockTransferSnValidator extends AbstractValidator
{
    const NOT_SCALAR = 'notScalar';
    const STOCK_TRANSFER_SN_EXISTS = 'stockTransferSnExists';

    protected $messageTemplates = [
        self::NOT_SCALAR => "这不是一个标准输入值",
        self::STOCK_TRANSFER_SN_EXISTS => "该库存调拨单号已经存在"
    ];

    private $entityManager;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager = $options['entityManager'];
        }

        parent::__construct($options);
    }

    public function isValid($value): bool
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $stockTransferInfo = $this->entityManager->getRepository(StockTransfer::class)->findOneBy(['transferSn' => $value]);
        if($stockTransferInfo != null) $isValid = false;
        else $isValid = true;

        if(!$isValid) $this->error(self::STOCK_TRANSFER_SN_EXISTS);

        return $isValid;
    }
}