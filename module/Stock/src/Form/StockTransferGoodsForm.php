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

namespace Stock\Form;

use Laminas\Form\Form;
use Stock\Validator\StockTransferGoodsArrayValidator;

class StockTransferGoodsForm extends Form
{
    private $entityManager;

    public function __construct($entityManager = null, $name = 'stock-transfer-goods-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'goodsId'
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'transferGoodsNum',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'goodsId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name' => StockTransferGoodsArrayValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'goodsField'    => 'goodsId'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'transferGoodsNum',
            'required'  => true,
            'validators'=> [
                [
                    'name' => StockTransferGoodsArrayValidator::class,
                    'options' => [
                        'goodsField' => 'transferGoodsNum'
                    ]
                ]
            ]
        ]);
    }
}