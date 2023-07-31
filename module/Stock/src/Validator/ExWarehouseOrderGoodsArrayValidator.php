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
use Store\Entity\WarehouseGoods;

class ExWarehouseOrderGoodsArrayValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const GOODS_NOT_EXISTS      = 'goodsNotExists';
    const GOODS_PRICE_NOT_MINUS = 'goodsPriceNotMinus';
    const GOODS_NOT_WAREHOUSE   = 'goodsNotWarehouse';
    const EX_GOODS_NUM_WAREHOUSE_ERROR = 'exGoodsNumWarehouseError';
    const EX_NUM_NOT_ZERO       = 'exNumNotZero';
    const GOODS_AMOUNT_NOT_MINUS= 'goodsAmountNotMinus';
    const GOODS_TAX_NOT_MINUS   = 'goodsTaxNotMinus';

    private $entityManager;
    private $goodsField;

    protected $messageTemplates = [
        self::NOT_SCALAR            => "不能为空",
        self::GOODS_NOT_EXISTS      => "商品不存在",
        self::GOODS_PRICE_NOT_MINUS => "商品出库单价不能为负数",
        self::GOODS_NOT_WAREHOUSE   => "商品在仓库中不存在",
        self::EX_GOODS_NUM_WAREHOUSE_ERROR => '该商品在仓库中的数量小于出库量',
        self::EX_NUM_NOT_ZERO       => "出库数量不能小于等于0",
        self::GOODS_AMOUNT_NOT_MINUS=> "商品总价不能为负数",
        self::GOODS_TAX_NOT_MINUS   => "税金不能为负数"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager'])) $this->entityManager = $options['entityManager'];
        }
        $this->goodsField = $options['goodsField'];

        parent::__construct($options);
    }

    public function isValid($value, $context=null): bool
    {
        if (!is_array($value) && !empty($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        switch ($this->goodsField) {
            case 'goodsId':
                foreach ($value as $key => $item) {
                    $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $item]);
                    if(!$goodsInfo) {
                        $this->error(self::GOODS_NOT_EXISTS);
                        return false;
                    }

                    $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['goodsId' => $item, 'warehouseId' => $context['warehouseId']]);
                    if (!$warehouseGoods) {
                        $this->error(self::GOODS_NOT_WAREHOUSE);
                        return false;
                    }
                    if ($warehouseGoods->getWarehouseGoodsStock() < $context['goodsExNum'][$key]) {
                        $this->error(self::EX_GOODS_NUM_WAREHOUSE_ERROR);
                        return false;
                    }
                }
                break;

            case 'goodsPrice':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_PRICE_NOT_MINUS);
                    return false;
                }
                break;

            case 'goodsTax':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_TAX_NOT_MINUS);
                    return false;
                }
                break;

            case 'goodsExNum':
                $array = array_filter($value, function ($k) { return $k <= 0; });
                if(!empty($array)) {
                    $this->error(self::EX_NUM_NOT_ZERO);
                    return false;
                }
                break;

            case 'goodsAmount':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_AMOUNT_NOT_MINUS);
                    return false;
                }
                break;
        }

        return true;
    }
}