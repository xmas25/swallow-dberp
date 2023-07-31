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

namespace Install;

use Install\Controller\InstallController;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'erp-install' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/install[/:action[/:id]]',
                    'defaults' => [
                        'controller' => InstallController::class,
                        'action'    => 'index'
                    ]
                ]
            ]
        ]
    ],

    'controllers' => [
        'factories' => [
            InstallController::class      => InvokableFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => []
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
        'template_map' => [
            'install/layout' => __DIR__ . '/../view/install/layout.phtml'
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
];