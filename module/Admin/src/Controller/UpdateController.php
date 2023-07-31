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

use Admin\AdminTrait\AdminTrait;
use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class UpdateController  extends AbstractActionController
{
    use AdminTrait;

    private $translator;
    private $entityManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
    }

    /**
     * 更新包列表
     * @return array|\Laminas\Http\Response|\Laminas\View\Model\ViewModel
     */
    public function indexAction()
    {
        $result = $this->adminCommon()->dberpApiService('updateErpPackageList');
        if ($result == null) {
            $view = new ViewModel();
            $view->setTemplate('admin/update/no-bind-update');
            return $view->setVariables([]);
        }

        return ['result' => $result];
    }

    /**
     * 获取更新包详情
     * @return array|\Laminas\Http\Response
     */
    public function updatePackageInfoAction()
    {
        $packageId  = (int) $this->params()->fromRoute('id');
        $result     = $this->adminCommon()->dberpApiService('updateErpPackageInfo', ['id' => $packageId]);
        if (
            !$result
            || ($result['code'] == 200 && empty($result['result']))
            || $result['code'] == 400
        ) return $this->redirect()->toRoute('update');

        $serviceBind = Common::readConfigFile('erpService');

        return ['result' => $result['result'], 'serviceBind' => $serviceBind];
    }

    /**
     * 更新执行
     * @return JsonModel
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function startErpPackageAction()
    {
        $packageId  = (int) $this->params()->fromRoute('id');
        $result     = $this->adminCommon()->dberpApiService('updateErpPackageInfo', ['id' => $packageId]);
        if (
            !$result
            || ($result['code'] == 200 && empty($result['result']))
            || $result['code'] == 400
        ) return new JsonModel(['state' => 'false', 'message' => $this->translator->translate('更新包信息错误，无法进行更新。可能原因：本地测试更新。')]);

        $packageInfo = $this->onlineOperationPackage($result['result']['packageInfo'], ['id' => $packageId], 'update');
        if (isset($packageInfo['state']) && $packageInfo['state'] == 'false' && $packageInfo['message']) return new JsonModel($packageInfo);

        //系统更新版本信息
        $shopVersion = "<?php\n";
        $shopVersion .= "const DBERP_VERSION = '".$packageInfo['versionName']."';\n";
        $shopVersion .= "const DBERP_VERSION_NUMBER = ".$packageInfo['versionNumber'].";\n";
        file_put_contents('data/erpVersion.php', $shopVersion);

        //更新缓存
        Common::deleteDirAndFile('data/cache', false);
        Common::opcacheReset();

        $this->adminCommon()->addOperLog(sprintf($this->translator->translate('更新包 %s 更新完成!如果还有其他更新包，请继续更新。'), $packageInfo['packageUpdateName']), $this->translator->translate('系统更新'));

        return new JsonModel(['state' => 'true', 'message' => $this->translator->translate('更新完成')]);
    }
}