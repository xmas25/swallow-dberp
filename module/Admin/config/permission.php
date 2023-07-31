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

namespace Admin;

use Admin\Controller\AdminController;
use Admin\Controller\AdminGroupController;
use Admin\Controller\AppController;
use Admin\Controller\OperLogController;
use Admin\Controller\RegionController;
use Admin\Controller\ServiceBindController;
use Admin\Controller\SystemController;
use Admin\Controller\UpdateController;

return [
    'Admin' => [
        'name' => '系统',
        'controllers' => [
            SystemController::class => [
                'name' => '系统设置',
                'action' => ['index'],
                'actionNames' => [
                    'index'         => '系统设置'
                ]
            ],
            AdminController::class => [
                'name' => '管理员',
                'action' => ['index', 'add', 'edit', 'delete', 'changePassword'],
                'actionNames' => [
                    'index' => '管理员列表',
                    'add'   => '管理员添加',
                    'edit'  => '管理员编辑',
                    'delete'=> '管理员删除',
                    'changePassword' => '修改密码'
                ]
            ],
            AdminGroupController::class => [
                'name' => '管理员组',
                'action' => ['adminGroupList', 'addAdminGroup', 'editAdminGroup', 'deleteAdminGroup'],
                'actionNames' => [
                    'adminGroupList'    => '管理员组列表',
                    'addAdminGroup'     => '添加管理员组',
                    'editAdminGroup'    => '编辑管理员组',
                    'deleteAdminGroup'  => '删除管理员组'
                ]
            ],
            RegionController::class => [
                'name' => '地区',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '地区列表',
                    'add'   => '添加地区',
                    'edit'  => '编辑地区',
                    'delete'=> '删除地区'
                ]
            ],
            AppController::class => [
                'name' => '商城绑定',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '商城列表',
                    'add'   => '添加商城',
                    'edit'  => '编辑商城',
                    'delete'=> '删除商城'
                ]
            ],
            OperLogController::class => [
                'name' => '操作日志',
                'action' => ['index', 'clearOperLog'],
                'actionNames' => [
                    'index'         => '查看日志',
                    'clearOperLog'  => '删除日志'
                ]
            ],
            ServiceBindController::class => [
                'name' => '服务绑定',
                'action' => ['index', 'clearServiceBind'],
                'actionNames' => [
                    'index' => '服务绑定',
                    'clearServiceBind' => '服务解绑'
                ]
            ],
            UpdateController::class => [
                'name' => '系统更新',
                'action' => ['index', 'updateErpPackageInfo', 'startErpPackage'],
                'actionNames' => [
                    'index' => '更新包列表',
                    'updateErpPackageInfo'  => '查看更新包详情',
                    'startErpPackage'       => '更新包更新'
                ]
            ],
        ]
    ]
];