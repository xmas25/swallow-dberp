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

namespace Api\Form;

use Laminas\Form\Form;

class OrderDeliveryForm extends Form
{
    public function __construct($name = 'order-delivery-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add(['type' => 'text', 'name' => 'order_sn']);
        $this->add(['type' => 'text', 'name' => 'oper_time']);
        $this->add(['type' => 'text', 'name' => 'delivery_number']);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'order_sn',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
        $inputFilter->add([
            'name'      => 'oper_time',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
        $inputFilter->add([
            'name'      => 'delivery_number',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
    }
}