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

use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, [$this, 'installLayout']);
    }

    public function installLayout(MvcEvent $event)
    {
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        if(substr($controllerName, 0, strpos($controllerName, '\\')) == __NAMESPACE__) {
            $viewModel = $event->getViewModel();
            $viewModel->setTemplate('install/layout');
        }
    }
}