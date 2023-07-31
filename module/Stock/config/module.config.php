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

namespace Stock;

use Stock\Controller\ExWarehouseController;
use Stock\Controller\Factory\ExWarehouseControllerFactory;
use Stock\Controller\Factory\IndexControllerFactory;
use Stock\Controller\Factory\StockCheckControllerFactory;
use Stock\Controller\Factory\StockTransferControllerFactory;
use Stock\Controller\IndexController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Stock\Controller\StockCheckController;
use Stock\Controller\StockTransferController;
use Stock\Plugin\Factory\StockPluginFactory;
use Stock\Plugin\StockPlugin;
use Stock\Service\ExWarehouseOrderGoodsManager;
use Stock\Service\ExWarehouseOrderManager;
use Stock\Service\Factory\ExWarehouseOrderGoodsManagerFactory;
use Stock\Service\Factory\ExWarehouseOrderManagerFactory;
use Stock\Service\Factory\OtherWarehouseOrderGoodsManagerFactory;
use Stock\Service\Factory\OtherWarehouseOrderManagerFactory;
use Stock\Service\Factory\StockCheckGoodsManagerFactory;
use Stock\Service\Factory\StockCheckManagerFactory;
use Stock\Service\Factory\StockTransferGoodsManagerFactory;
use Stock\Service\Factory\StockTransferManagerFactory;
use Stock\Service\OtherWarehouseOrderGoodsManager;
use Stock\Service\OtherWarehouseOrderManager;
use Stock\Service\StockCheckGoodsManager;
use Stock\Service\StockCheckManager;
use Stock\Service\StockTransferGoodsManager;
use Stock\Service\StockTransferManager;
use Stock\View\Helper\Factory\StockHelperFactory;
use Stock\View\Helper\StockHelper;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'erp-stock' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock[/:action[/:id]]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'stock-check' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock-check[/:action[/:id]]',
                    'defaults' => [
                        'controller' => StockCheckController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'stock-transfer' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock-transfer[/:action[/:id]]',
                    'defaults' => [
                        'controller' => StockTransferController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'stock-ex' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock-ex[/:action[/:id]]',
                    'defaults' => [
                        'controller' => ExWarehouseController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]

    ],

    'controllers' => [
        'factories' => [
            IndexController::class          => IndexControllerFactory::class,
            StockCheckController::class     => StockCheckControllerFactory::class,
            StockTransferController::class  => StockTransferControllerFactory::class,
            ExWarehouseController::class    => ExWarehouseControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            OtherWarehouseOrderManager::class       => OtherWarehouseOrderManagerFactory::class,
            OtherWarehouseOrderGoodsManager::class  => OtherWarehouseOrderGoodsManagerFactory::class,
            StockCheckManager::class                => StockCheckManagerFactory::class,
            StockCheckGoodsManager::class           => StockCheckGoodsManagerFactory::class,
            StockTransferManager::class             => StockTransferManagerFactory::class,
            StockTransferGoodsManager::class        => StockTransferGoodsManagerFactory::class,
            ExWarehouseOrderManager::class          => ExWarehouseOrderManagerFactory::class,
            ExWarehouseOrderGoodsManager::class     => ExWarehouseOrderGoodsManagerFactory::class
        ]
    ],

    'listeners' => [],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            StockPlugin::class => StockPluginFactory::class
        ],
        'aliases'   => [
            'stockPlugin' => StockPlugin::class
        ]
    ],

    'view_helpers' => [
        'factories' => [
            StockHelper::class  => StockHelperFactory::class
        ],
        'aliases' => [
            'stockHelper' => StockHelper::class
        ],
    ],

    'session_containers' => [

    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../data/language',
                'pattern' => '%s.mo'
            ]
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];