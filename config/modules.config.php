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

return [
    'Laminas\Serializer',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\Hydrator',
    'Laminas\Paginator',
    'Laminas\ServiceManager\Di',
    'Laminas\Session',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Mvc\I18n',
    'Laminas\I18n',
    'Laminas\Mvc\Console',
    'Laminas\Log',
    'Laminas\Form',
    'Laminas\Db',
    'Laminas\Cache',
    'Laminas\Router',
    'Laminas\Validator',

    'DoctrineModule',
    'DoctrineORMModule',

    'Admin',    //
    'Store',    //
    'Customer', //
    'Purchase', //
    'Finance',  //
    'Sales',    //
    'Api',      //
    'Shop',     //
    'Report',   //
    'Stock',    //
    'Install',
];
