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
use Store\Entity\Goods;

class StockTransferGoodsArrayValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const GOODS_NOT_EXISTS      = 'goodsNotExists';
    const TRANSFER_GOODS_NUM_NOT_ZERO = 'transferGoodsNumNotZero';

    private $entityManager;
    private $goodsField;

    protected $messageTemplates = [
        self::NOT_SCALAR            => "不能为空",
        self::GOODS_NOT_EXISTS      => "商品不存在",
        self::TRANSFER_GOODS_NUM_NOT_ZERO => "调拨数量不能小于等于0"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
        }
        $this->goodsField = $options['goodsField'];

        parent::__construct($options);
    }

    public function isValid($value): bool
    {
        if (!is_array($value) && !empty($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        switch ($this->goodsField) {
            case 'goodsId':
                foreach ($value as $item) {
                    $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $item]);
                    if(!$goodsInfo) {
                        $this->error(self::GOODS_NOT_EXISTS);
                        return false;
                    }
                }
                break;

            case 'transferGoodsNum':
                $array = array_filter($value, function ($k) { return $k <= 0; });
                if(!empty($array)) {
                    $this->error(self::TRANSFER_GOODS_NUM_NOT_ZERO);
                    return false;
                }
                break;
        }

        return true;
    }
}