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

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

//如果在开发状态时，注释下面语句；在生产环境下，不要注释
error_reporting(0);

if (version_compare(PHP_VERSION, '7.4', '<') === true) exit('ERROR: Your PHP version is ' . PHP_VERSION . '. DBShop requires PHP 7.4 or newer.<br><br>错误：您的 PHP 版本是 ' . PHP_VERSION . '。DBErp系统支持 PHP7.4或者更高版本。');

include './vendor/autoload.php';

if (!file_exists('data/install.lock')) {
    $request= new \Laminas\Http\PhpEnvironment\Request();
    $urlStr = $request->getUriString();
    if (!in_array(basename($urlStr), ['install', 'nextStep', 'installErp', 'installFinish'])) {
        if (is_dir('data/cache/') && !is_writable('data/cache/')) echo "<strong style='color: red;'>data/cache/ 没有写权限</strong><br>";
        exit('<a href="'.rtrim($request->getBaseUrl(), '/').'/install">click Install DBErp</a>');
    }
}

$appConfig = require './config/application.config.php';
if (file_exists('./config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require  './config/development.config.php');
}

//erp版本信息
include "./data/erpVersion.php";

/**
 * 动态加载
 */
$moduleExtendConfig = require './data/moduleExtend.php';
if(!empty($moduleExtendConfig)) {
    $loader = new \Composer\Autoload\ClassLoader();
    array_map(function ($module) use($loader) {
        $loader->addPsr4($module['key'], $module['value']);
        }, $moduleExtendConfig);
    $loader->register();
}



Application::init($appConfig)->run();
