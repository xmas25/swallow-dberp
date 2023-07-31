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

namespace Admin\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class CurrentRoute extends AbstractHelper
{
    protected $routeMatch;

    public function __construct($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    public function getController()
    {
        if($this->routeMatch == null) return false;

        $controller = $this->routeMatch->getParam('controller');
        //$controller = explode('\\', strtolower($controller));
        $controller = explode('\\', $controller);
        return array_pop($controller);
    }

    public function getAction()
    {
        if($this->routeMatch == null) return false;

       return $this->routeMatch->getParam('action');
    }

    public function getModule()
    {
        if($this->routeMatch == null) return false;

        $controller = $this->routeMatch->getParam('controller');
        $module     = $this->routeMatch->getParam('__NAMESPACE__');

       // $controller = explode('\\', strtolower($controller));
        $controller = explode('\\', $controller);
       // $module     = explode('\\', strtolower($module));
        $module     = explode('\\', $module);
        if($module[0] === '' && count($controller) === 3) {
            $module[0] = $controller[0];
        }

        return $module[0];
    }

    public function getRoute()
    {
        if($this->routeMatch == null) return false;

        return $this->routeMatch->getMatchedRouteName();
    }
}