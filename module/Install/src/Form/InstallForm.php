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

namespace Install\Form;

use Install\Validator\DatabaseValidator;
use Laminas\Form\Form;
use Laminas\Validator\Hostname;

class InstallForm extends Form
{
    public function __construct( $name = 'shop-install-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'dbHost',
            'attributes'    => [
                'id'            => 'dbHost',
                'class'         => 'form-control',
                'value' => 'localhost',
                'style' => 'width:500px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'dbName',
            'attributes'    => [
                'id'    => 'dbName',
                'class' => 'form-control',
                'value' => 'dberp',
                'style' => 'width:200px;height:25px;display: unset;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'dbUser',
            'attributes'    => [
                'id'            => 'dbUser',
                'class'         => 'form-control',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'dbPassword',
            'attributes'    => [
                'id'            => 'dbPassword',
                'class'         => 'form-control',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'dbPort',
            'attributes'    => [
                'id'            => 'dbPort',
                'value'     => '3306',
                'class'         => 'form-control',
                'style' => 'width:100px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'adminUser',
            'attributes'    => [
                'id'            => 'adminUser',
                'class'         => 'form-control',
                'value' => 'admin',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'adminPassword',
            'attributes'    => [
                'id'            => 'adminPassword',
                'class'         => 'form-control',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'adminComPassword',
            'attributes'    => [
                'id'            => 'adminComPassword',
                'class'         => 'form-control',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'adminEmail',
            'attributes'    => [
                'id'            => 'adminEmail',
                'class'         => 'form-control',
                'value' => 'admin@admin.com',
                'style' => 'width:200px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'shopName',
            'attributes'    => [
                'id'            => 'shopName',
                'class'         => 'form-control',
                'value' => '北京珑大钜商科技有限公司',
                'style' => 'width:400px;height:25px;'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'timeZone',
            'attributes'    => [
                'id'            => 'timeZone',
                'class'         => 'form-control',
                'style' => 'width:200px;'
            ]
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'dbHost',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'dbName',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'dbUser',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'dbPassword',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ],
            'validators'=> [
                ['name' => DatabaseValidator::class]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'dbPort',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminUser',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminPassword',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 6
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminComPassword',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'Identical',
                    'options'   => [
                        'token' => 'adminPassword'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminEmail',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 100
                    ]
                ],
                [
                    'name'      => 'EmailAddress',
                    'options'   => [
                        'allow'         => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false
                    ]
                ]
            ]
        ]);
        $inputFilter->add([
            'name'      => 'shopName',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);
    }
}