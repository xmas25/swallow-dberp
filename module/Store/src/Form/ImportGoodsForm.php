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

namespace Store\Form;

use Laminas\Form\Form;

class ImportGoodsForm extends Form
{
    public function __construct($name = 'import-goods-form', $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'file',
            'name'  => 'importFile',
            'attributes'    => [
                'id'            => 'importFile',
                'class'         => 'form-control'
            ]
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'type'      => 'Laminas\InputFilter\FileInput',
            'name'      => 'importFile',
            'required'  => true,
            'validators'=> [
                [
                    'name' => 'fileExtension',
                    'options' => [
                        'extension' => ['xlsx']
                    ]
                ],
                [
                    'name' => 'FileMimeType',
                    'options' => [
                        'mimeType' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream']
                    ]
                ]
            ]
        ]);
    }
}