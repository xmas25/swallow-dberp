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

use Admin\Data\Config;
use Admin\Form\AdminUserLoginForm;
use Admin\Service\AdminUserManager;
use Admin\Service\AuthManager;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\Result;
use Laminas\Config\Factory;
use Laminas\Filter\StaticFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AdminUserManager
     */
    private $adminUserManager;

    /**
     * @var AuthManager
     */
    private $authManager;

    public function __construct(
        Translator          $translator,
        EntityManager       $entityManager,
        AdminUserManager    $adminUserManager,
        AuthManager         $authManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->adminUserManager = $adminUserManager;
        $this->authManager      = $authManager;
    }

    /**
     * 管理员登录
     * @return \Laminas\Http\Response|ViewModel
     */
    public function indexAction()
    {
        if($this->authManager->checkLogin()) return $this->redirect()->toRoute('home');

        $view = new ViewModel();
        $view->setTerminal(true);

        $form = new AdminUserLoginForm();

        $array = ['form'=>$form];

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $result = $this->authManager->login($data['admin_name'], $data['admin_passwd']);
                if($result->getCode() == Result::SUCCESS) {
                    $this->adminCommon()->addOperLog($data['admin_name'], $this->translator->translate('管理员登录'), false);

                    return $this->redirect()->toRoute('home');
                } else $array['loginError'] = false;
            }
        }

        return $view->setVariables($array);
    }

    /**
     * 管理员退出
     * @return \Laminas\Http\Response
     */
    public function logoutAction()
    {
        $this->authManager->logout();
        return $this->redirect()->toRoute('login');
    }

    /**
     * updateKey输出
     * @return mixed
     */
    public function outputUpdateKeyAction()
    {
        $updateKey      = '';
        $actionName     = StaticFilter::execute($this->params()->fromQuery('actionName'), 'HtmlEntities');
        $updateKeyArray = Factory::fromFile(Config::PACKAGE_UPDATE_KEY_FILE);
        if (isset($updateKeyArray[$actionName]) && !empty($updateKeyArray[$actionName])) $updateKey = $updateKeyArray[$actionName];

        return $this->response->setContent($updateKey);
    }
}
