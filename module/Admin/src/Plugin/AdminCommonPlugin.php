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

namespace Admin\Plugin;

use Admin\Data\Common;
use Admin\Data\Config;
use Admin\Entity\AdminUserGroup;
use Admin\Entity\App;
use Admin\Entity\Region;
use Admin\Service\OperlogManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Laminas\Config\Factory;
use Laminas\Crypt\BlockCipher;
use Laminas\Http\Client;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\I18n\Translator;
use Laminas\Session\Container;
use Laminas\Validator\Csrf;

class AdminCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;
    private $operlogManager;
    private $sessionAdmin;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator,
        OperlogManager  $operlogManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->operlogManager   = $operlogManager;

        if($this->sessionAdmin == null) {
            $this->sessionAdmin     = new Container('admin');
        }
    }

    /**
     * 验证删除的CSRF Token
     * @return bool
     */
    public function validatorCsrf()
    {
        $csrfValue = $this->getController()->getRequest()->getQuery('qToken');
        $csrf = new Csrf(['name' => 'queryToken']);
        if(!$csrf->isValid($csrfValue)) {
            $this->getController()->flashMessenger()->addErrorMessage($this->translator->translate('不正确的请求！'));
            return false;
        }
        return true;
    }

    /**
     * 公共分页方法
     * @param Query $query
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @param bool $fetchJoinCollection
     * @return \Laminas\Paginator\Paginator
     */
    public function erpPaginator(Query $query, int $pageNumber, $itemCountPerPage = 16, $fetchJoinCollection = false)
    {
        $adapter    = new DoctrinePaginator(new Paginator($query, $fetchJoinCollection));
        $paginator  = new \Laminas\Paginator\Paginator($adapter);
        $paginator->setItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($pageNumber);

        return $paginator;
    }

    /**
     * 返回上一页
     * @return mixed
     */
    public function toReferer()
    {
        $referer = $this->getController()->params()->fromHeader('Referer');
        if($referer) {
            $refererUrl     = $referer->uri()->getPath();
            $refererHost    = $referer->uri()->getHost();
            $host           = $this->getController()->getRequest()->getUri()->getHost();
            if ($refererUrl && $refererHost == $host) {
                return $this->getController()->redirect()->toUrl($refererUrl);
            }
        }
        return $this->getController()->redirect()->toRoute('home');
    }

    /**
     * 添加操作日志 同时输出 flashMessenger
     * @param $logBody
     * @param $operClassName
     * @param $message
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOperLog($logBody, $operClassName, $message = true)
    {
        $this->operlogManager->addOperLog(
            [
                'logOperUser'       => $this->sessionAdmin->admin_name,
                'logOperUserGroup'  => $this->sessionAdmin->admin_group_name,
                'logTime'           => time(),
                'logIp'             => $this->getController()->getRequest()->getServer('REMOTE_ADDR'),
                'logBody'           => '['.$operClassName.'] '.$logBody
            ]
        );

        if($message) $this->getController()->flashMessenger()->addSuccessMessage($logBody);
    }

    /**
     * 获取地区下级
     * @param int $topId
     * @return array|object[]|null
     */
    public function getRegionSub($topId = 0)
    {
        $region = $this->entityManager->getRepository(Region::class)->findBy(['regionTopId' => $topId], ['regionSort' => 'ASC']);
        return $region ? $region : null;
    }

    /**
     * 获取绑定商城列表
     * @param string $topName
     * @return array
     */
    public function appShopOptions($topName = '')
    {
        $appArray   = [0 => empty($topName) ? $this->translator->translate('选择商城') : $topName];
        $appList    = $this->entityManager->getRepository(App::class)->findBy([]);
        if($appList) {
            foreach ($appList as $value) {
                $appArray[$value->getAppId()] = $value->getAppName();
            }
        }
        return $appArray;
    }

    /**
     * 获取品牌列表
     * @param string $topName
     * @return array
     */
    public function adminGroupOptions($topName = '')
    {
        $groupList  = [0 => empty($topName) ? $this->translator->translate('选择管理组') : $topName];
        $group      = $this->entityManager->getRepository(AdminUserGroup::class)->findBy([], ['adminGroupId' => 'ASC']);
        if($group) {
            foreach ($group as $value) {
                $groupList[$value->getAdminGroupId()] = $value->getAdminGroupName();
            }
        }
        return $groupList;
    }

    /**
     * 获取时区数组
     * @return mixed
     */
    public function timezoneArray()
    {
        $localLanguage = $this->translator->getTranslator()->getLocale();
        if(!file_exists('data/moduleData/System/TimezoneData/' . $localLanguage . '.php')) $localLanguage = 'zh_CN';

        return include 'data/moduleData/System/TimezoneData/' . $localLanguage . '.php';
    }

    /**
     * 平铺无限分类-暂时未使用
     * @param $items
     * @param string $id
     * @param string $pid
     * @param string $name
     * @param string $path
     * @param string $son
     * @return array
     */
    public function genTree(
        $items,
        $id='getGoodsCategoryId',
        $pid='getGoodsCategoryTopId',
        $name='getGoodsCategoryName',
        $path='getGoodsCategoryPath',
        $son = 'children'
    )
    {
        $tree = []; //格式化的树
        $tmpMap = [];  //临时扁平数据

        foreach ($items as $item) {
            $tmpMap[$item->$id()] = [
                'id'    => $item->$id(),
                'top_id'=> $item->$pid(),
                'name'  => $item->$name(),
                'path'  => $item->$path()
            ];
        }

        foreach ($items as $item) {
            if (isset($tmpMap[$item->$pid()])) {
                $tmpMap[$item->$pid()][$son][] = &$tmpMap[$item->$id()];
                //$tmpMap[$item->$pid()][$son][] = $tmpMap[$item->$id()];
            } else {
                $tree[] = &$tmpMap[$item->$id()];
                //$tree[] = $tmpMap[$item->$id()];
            }
        }
        unset($tmpMap);
        return $tree;
    }

    /**
     * 服务调用
     * @param $action
     * @param array $data
     * @param string $path
     * @return mixed|null
     */
    public function dberpApiService($action, $data = [], $path = '')
    {
        $serviceConfig = Common::readConfigFile('erpService');
        if (empty($path)) {
            if (!$serviceConfig['key'] || !$serviceConfig['code'] || !$serviceConfig['userName']) return null;

            $blockCipher = BlockCipher::factory('openssl', ['algo' => 'aes', 'mode' => 'gcm']);
            $blockCipher->setKey($serviceConfig['key']);
            $dataStr = $blockCipher->encrypt(Json::encode(array_merge($data,
                [
                    'code'          => $serviceConfig['code'],
                    'scheme'        => $this->getController()->getRequest()->getUri()->getScheme(),
                    'host'          => $this->getController()->getRequest()->getUri()->getHost(),
                    'baseUrl'       => $this->getController()->getRequest()->getBaseUrl(),
                    'port'          => $this->getController()->getRequest()->getUri()->getPort(),
                    'versionNumber' => DBERP_VERSION_NUMBER
                ]
            )));
        } else {//单独调用不需要走加密接口
            $dataStr = serialize(array_merge($data,
                [
                    'scheme'        => $this->getController()->getRequest()->getUri()->getScheme(),
                    'host'          => $this->getController()->getRequest()->getUri()->getHost(),
                    'baseUrl'       => $this->getController()->getRequest()->getBaseUrl(),
                    'port'          => $this->getController()->getRequest()->getUri()->getPort(),
                    'versionNumber' => DBERP_VERSION_NUMBER
                ]));
        }

        $client = new Client(Config::SERVICE_API_URL . $path, [
            'adapter' => 'Laminas\Http\Client\Adapter\Curl',
            'curloptions' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        $client->setHeaders(['Accept' => 'application/json']);
        $client->setMethod('POST');
        $client->setParameterPost([
            'userName'      => $serviceConfig['userName'],
            'systemType'    => 'dberp',
            'action'        => $action,
            'dataStr'       => $dataStr
        ]);

        try {
            $response = $client->send();
            if ($response->isSuccess()) {
                return Json::decode($response->getBody(), Json::TYPE_ARRAY);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * 下载服务中的包文件
     * @param $action
     * @param $data
     * @param $localPackage
     * @return bool
     */
    public function dberpDownloadPackage($action, $data, $localPackage)
    {
        $serviceConfig  = Common::readConfigFile('erpService');

        $updateKey = '';
        $clientGet = new Client(Config::SERVICE_API_URL . '/getUpdateKey', ['adapter' => 'Laminas\Http\Client\Adapter\Curl', 'curloptions' => [CURLOPT_SSL_VERIFYPEER => false]]);
        $clientGet->setMethod('POST');
        $clientGet->setParameterPost(['userName' => $serviceConfig['userName'], 'actionName' => $action, 'systemType' => 'dberp', 'hostUrl' => $this->getController()->getRequest()->getUri()->getHost()]);
        $responseGet = $clientGet->send();
        if ($responseGet->isSuccess()) $updateKey = $responseGet->getBody();
        if (empty($action) || empty($updateKey)) return false;

        $blockCipher    = BlockCipher::factory('openssl', ['algo' => 'aes', 'mode' => 'gcm']);
        $blockCipher->setKey($serviceConfig['key']);
        $dataStr = $blockCipher->encrypt(Json::encode(array_merge($data,
            [
                'code'          => $serviceConfig['code'],
                'scheme'        => $this->getController()->getRequest()->getUri()->getScheme(),
                'host'          => $this->getController()->getRequest()->getUri()->getHost(),
                'baseUrl'       => $this->getController()->getRequest()->getBaseUrl(),
                'port'          => $this->getController()->getRequest()->getUri()->getPort(),
                'updateKey'     => $updateKey,
                'versionNumber' => DBERP_VERSION_NUMBER
            ]
        )));


        $updateKeyIni = Factory::fromFile(Config::PACKAGE_UPDATE_KEY_FILE);
        $updateKeyIni[$action] = $updateKey;
        Factory::toFile(Config::PACKAGE_UPDATE_KEY_FILE, $updateKeyIni);

        $client = new Client(Config::SERVICE_API_URL, [
            'adapter' => 'Laminas\Http\Client\Adapter\Curl',
            'curloptions' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HEADER => 0
            ],
            'timeout' => 300
        ]);
        $client->setMethod('POST');
        $client->setParameterPost([
            'userName'      => $serviceConfig['userName'],
            'systemType'    => 'dberp',
            'action'        => $action,
            'dataStr'       => $dataStr
        ]);
        $response = $client->send();
        if ($response->isSuccess()) {
            if ($response->getBody() == 'updateKeyError') return 'keyError';
            else {
                $signedUrl = $response->getBody();
                if (!empty($signedUrl)) {
                    $downClient     = new Client($signedUrl, ['adapter' => 'Laminas\Http\Client\Adapter\Curl', 'curloptions' => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HEADER => 0], 'timeout' => 300]);
                    $downClient->setMethod('GET');
                    $downResponse   = $downClient->send();
                    if ($downResponse->isSuccess()) {
                        file_put_contents($localPackage, $downResponse->getBody());
                        return true;
                    }
                }
            }
        }
        return false;
    }
}