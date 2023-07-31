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

use Doctrine\DBAL\Driver\PDO\MySQL\Driver as PDOMySqlDriver;

return [
    'translator'    => [
        'locale' => 'zh_CN',
        'translation_file_patterns' => [
            [
                'type' => 'phpArray',
                'base_dir' => \Admin\Validator\Resources::getBasePath(),
                'pattern'  => \Admin\Validator\Resources::getPatternForValidator()
            ],
            [
                'type' => 'phpArray',
                'base_dir' => \Admin\Validator\Resources::getBasePath(),
                'pattern'  => \Admin\Validator\Resources::getPatternForCaptcha()
            ],
        ]
    ],

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => array_merge(require 'data/erpDatabase.php', ['driverOptions' => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4', sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"]])
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'proxy_dir' => 'data/cache/Doctrine/DoctrineORMModule/Proxy'
            ]
        ]
    ],
];
