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

namespace Admin\Form;

use Laminas\Form\Form;

class SystemForm extends Form
{
    public function __construct($name = 'system-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'company_name|base',
            'attributes'    => [
                'id'            => 'company_name|base',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'website_icp|base',
            'attributes'    => [
                'id'            => 'website_icp|base',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'checkbox',
            'name'  => 'shop_service|base',
            'attributes' => [
                'value' => 1
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'website_timezone|base',
            'attributes'    => [
                'id'            => 'website_timezone|base',
                'class'         => 'form-control'
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'company_name|base',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'website_icp|base',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'shop_service|base',
            'required'  => false,
            'validators'=> [
                [
                    'name'      => 'InArray',
                    'options'   => [
                        'haystack'  => [0, 1]
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'website_timezone|base',
            'required'  => false
        ]);
    }
}