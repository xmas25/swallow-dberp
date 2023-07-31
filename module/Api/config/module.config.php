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

namespace Api;

use Api\Controller\Factory\IndexControllerFactory;
use Api\Controller\Factory\OtherControllerFactory;
use Api\Controller\IndexController;
use Api\Controller\OtherController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'erp-api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'erp-other-api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/other-api[/:action]',
                    'defaults' => [
                        'controller' => OtherController::class,
                        'action'    => 'index'
                    ]
                ]
            ]
        ]

    ],

    'controllers' => [
        'factories' => [
            IndexController::class  => IndexControllerFactory::class,
            OtherController::class  => OtherControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [

        ]
    ],

    'listeners' => [],

    'controller_plugins' => [
        'factories' => [

        ],
        'aliases'   => [

        ]
    ],

    'view_helpers' => [
        'factories' => [

        ],
        'aliases' => [

        ],
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