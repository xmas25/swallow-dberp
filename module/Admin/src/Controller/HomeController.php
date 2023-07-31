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

namespace Admin\Controller;

use Admin\Data\Common;
use Admin\Entity\Plugin;
use Admin\Report\HomeReport;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\ViewModel;

class HomeController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $homeReport;
    private $i18nSessionContainer;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        HomeReport      $homeReport,
        $i18nSessionContainer
    )
    {
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->homeReport           = $homeReport;
        $this->i18nSessionContainer = $i18nSessionContainer;
    }

    /**
     * 后台首页
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $array['goodsCount']        = $this->homeReport->goodsCount();
        $array['purchaseAmount']    = $this->homeReport->purchaseAmount();
        $array['salesAmount']       = $this->homeReport->salesAmount();
        $array['customerCount']     = $this->homeReport->customerCount() + $this->homeReport->supplierCount();

        $array['purchaseOrder']     = $this->homeReport->purchaseOrderLimit();
        $array['salesOrder']        = $this->homeReport->salesOrderLimit();

        $array['serviceBind'] = Common::readConfigFile('erpService');

        $data = [];
        //$data['plugin'] = $this->pluginCodeArray();
        $result         = $this->adminCommon()->dberpApiService('empty', $data, '/latestNews');
        $array['newsList']      = $result['result']['newsList']??null;
        $array['newPackage']    = $result['result']['newPackage']??0;
        $array['pluginPackage'] = $result['result']['pluginPackage']??0;
        $array['dberpAuth']     = $result['result']['dberpAuth']??0;

        return $array;
    }

    /**
     * 无权限显示页面
     * @return ViewModel
     */
    public function notAuthorizedAction(): ViewModel
    {
        $view = new ViewModel();
        $view->setTerminal(true);

        $getArray   = $this->params()->fromQuery();

        $module     = '';
        $controller = $getArray['controllerName']??'';
        $action     = $getArray['actionName']??'';
        $lastPage   = $getArray['lastPage']??'';
        if (!empty($controller)) {
            $controller = explode('\\', $controller);
            $module     = $controller[0];
            $controller = $controller[count($controller) - 1];
        }

        return $view->setVariables(['module' => $module, 'action' => $action, 'controller' => $controller, 'lastPage' => $lastPage]);
    }

    /**
     * 获取插件编码
     * @return array
     */
    private function pluginCodeArray()
    {
        $codeArray = [];
        $pluginList= $this->entityManager->getRepository(Plugin::class)->findBy([]);
        if ($pluginList) foreach ($pluginList as $plugin) {
            $codeArray['pluginCode'][] = $plugin->getPluginCode();
            $codeArray['versionNumber'][$plugin->getPluginCode()] = $plugin->getPluginVersionNum();
        }

        return $codeArray;
    }

    /**
     * 设置语言
     * @return \Laminas\Http\Response
     */
    public function setLanguageAction()
    {
        $language = $this->params()->fromRoute('language', 'zh_CN');
        $this->i18nSessionContainer->language = $language;

        return $this->redirect()->toRoute('home');
    }
}