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

use Admin\Controller\IndexController;
use Admin\Service\AuthManager;
use Install\Controller\InstallController;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Laminas\Session\SessionManager;
use Laminas\Validator\AbstractValidator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        //错误日志记录
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError'], 99);
        //检查管理员权限
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(AbstractActionController::class, 'dispatch', [$this, 'checkAdminFilterAccess'], 99);

        //$eventManager->attach('dispatch', [$this, 'systemLanguage']);

        //第一时间启用会话配置
        $sessionManager = $event->getApplication()->getServiceManager()->get(SessionManager::class);
        /*$tableGateway   = new TableGateway('dberp_session', new Adapter([
            'driver'    => 'Pdo',
            'dsn'       => 'mysql:dbname=dberp;port=3306;host=localhost;charset=utf8',
            'username'  => 'root',
            'password'  => 'root',
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"
            ]
        ]));
        $saveHandler    = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
        $sessionManager->setSaveHandler($saveHandler);*/
        $sessionManager->start();

        $this->systemLanguage($event);
    }

    /**
     * 多语言设置
     * @param MvcEvent $event
     */
    protected function systemLanguage(MvcEvent $event) {
        $serviceManager = $event->getApplication()->getServiceManager();
        $config = $serviceManager->get('config');

        $container  = $serviceManager->get('I18nSessionContainer');

        $language   = $config['translator']['locale'];
        if(isset($container->language)) {
            $language = $container->language;
        }

        $translator = $serviceManager->get('MvcTranslator');
        //设置当前语言
        if($language != $config['translator']['locale']) {
            $translator->setLocale($language);
        }
        //服务器端验证进行语言设置
        AbstractValidator::setDefaultTranslator($translator);
    }
    /**
     * 权限检查
     * @param MvcEvent $event
     * @return mixed
     */
    public function checkAdminFilterAccess(MvcEvent $event)
    {
        $controller     = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);

        //如果是api模块，不进行后台权限验证
        if(substr($controllerName, 0, strpos($controllerName, '\\')) == 'Api') {
            return true;
        }

        $actionName     = $event->getRouteMatch()->getParam('action', null);
        $actionName     = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        $authManager= $event->getApplication()->getServiceManager()->get(AuthManager::class);

        if(!in_array($controllerName, [IndexController::class, InstallController::class])) {
            $state = $authManager->filterAccess($controllerName, $actionName);
            if($state == -1) {
                return $controller->redirect()->toRoute('login');
            }elseif($state == -2) {
                $referer = $controller->params()->fromHeader('Referer');
                return $controller->redirect()->toRoute('home/default', ['action' => 'notAuthorized'], ['query' => ['controllerName' => $controllerName, 'actionName' => $actionName, 'lastPage' => urlencode($referer->uri()->getPath().(!empty($referer->uri()->getQuery()) ? '?'.$referer->uri()->getQuery() : ''))]]);
            }
        }

        return true;
    }

    /**
     * 错误日志记录
     * @param MvcEvent $event
     */
    public function onError(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        if($exception != null) {
            $exceptionName = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $stackTrace = $exception->getTraceAsString();

            $errorMessage = $event->getError();
            $controllerName = $event->getController();

            $body = '';
            if(isset($_SERVER['REQUEST_URI'])) {
                $body .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
            }
            $body .= "Controller: $controllerName\n";
            $body .= "Error message: $errorMessage\n";
            $body .= "Exception: $exceptionName\n";
            $body .= "File: $file\n";
            $body .= "Line: $line\n";
            $body .= "Stack trace:\n" . $stackTrace."\n\n";

            $log     = new Logger();
            $errorLog= new Stream('./data/error/'.date("Y-m-d").'_error.log');
            $log->addWriter($errorLog)->err($body);
        }

    }
}
