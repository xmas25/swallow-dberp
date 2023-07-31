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

use Admin\Data\Config;
use Laminas\Form\Form;
use Stock\Validator\StockTransferSnValidator;
use Stock\Validator\StockTransferWarehouseValidator;

class StockTransferForm extends Form
{
    private $entityManager;

    public function __construct($entityManager = null, $name = 'stock-transfer-form', $options = [])
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
            'name'  => 'transferSn',
            'attributes'    => [
                'id'            => 'transferSn',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'transferInWarehouseId',
            'attributes'    => [
                'id'            => 'transferInWarehouseId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'transferOutWarehouseId',
            'attributes'    => [
                'id'            => 'transferOutWarehouseId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'transferAddTime',
            'attributes'    => [
                'id'            => 'transferAddTime',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'transferInfo',
            'attributes'    => [
                'id'            => 'transferInfo',
                'class'         => 'form-control',
                'rows'          => 3
            ]
        ]);

        $this->add([
            'type'  => 'csrf',
            'name'  => 'dberp_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout'  => Config::POST_TOKEN_TIMEOUT
                ]
            ]
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'transferSn',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 50
                    ]
                ],
                [
                    'name'      => StockTransferSnValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'transferInWarehouseId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0
                    ]
                ],
                [
                    'name'      => StockTransferWarehouseValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'inWarehouse'   => 'true'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'transferOutWarehouseId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0
                    ]
                ],
                [
                    'name'      => StockTransferWarehouseValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'transferAddTime',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'transferInfo',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 500
                    ]
                ]
            ]
        ]);
    }
}