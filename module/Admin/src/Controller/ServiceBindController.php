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
use Admin\Form\ServiceBindForm;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\ViewModel;

class ServiceBindController extends AbstractActionController
{
    private $translator;
    private $entityManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager
    )
    {
        $this->translator   = $translator;
        $this->entityManager= $entityManager;
    }

    /**
     * 服务绑定
     * @return ServiceBindForm[]|\Laminas\Http\Response|ViewModel
     */
    public function indexAction()
    {
        $form = new ServiceBindForm();
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->adminCommon()->addOperLog($this->translator->translate('服务绑定成功!'), $this->translator->translate('服务绑定'));
                return $this->redirect()->toRoute('service-bind');
            }
        }

        $serviceBind = Common::readConfigFile('erpService');
        if (
            isset($serviceBind['userName']) && !empty($serviceBind['userName'])
            && isset($serviceBind['key']) && !empty($serviceBind['key'])
            && isset($serviceBind['code']) && !empty($serviceBind['code'])
        ) {
            $viewMode = new ViewModel();
            $viewMode->setTemplate('admin/service-bind/finish.phtml');

            return $viewMode->setVariables($serviceBind);
        }

        return ['form' => $form];
    }

    /**
     * 服务解绑
     * @return \Laminas\Http\Response
     */
    public function clearServiceBindAction()
    {
        Common::writeConfigFile('erpService', ['userName' => '', 'key' => '', 'code' => '']);
        $this->adminCommon()->addOperLog($this->translator->translate('服务解绑成功!'), $this->translator->translate('服务绑定'));
        return $this->redirect()->toRoute('service-bind');
    }
}