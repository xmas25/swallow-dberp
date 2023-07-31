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

namespace Install\Controller;

use Admin\Data\Common;
use Install\Form\InstallForm;
use Laminas\Config\Factory;
use Laminas\Config\Writer\PhpArray;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\I18n\Translator\Translator;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

class InstallController extends AbstractActionController
{
    private $translator;
    private $session;
    private $db;

    public function __construct()
    {
        if (file_exists('data/install.lock')) {
            $request= new \Laminas\Http\PhpEnvironment\Request();
            exit('您已经安装完毕!<a href="'.$request->getBaseUrl().'">点击登录系统</a>');
        }

        $this->translator   = new Translator();
        $this->session      = new Container('erpSession');
    }

    /**
     * 授权协议显示
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->session->offsetSet('installStep', 1);

        return [];
    }

    /**
     * 下一步安装
     * @return array|\Laminas\Http\Response
     */
    public function nextStepAction()
    {
        if (!$this->session->offsetExists('installStep') || !in_array($this->session->offsetGet('installStep'), [1, 2])) return $this->redirect()->toRoute('erp-install');

        $checkDirArray = $this->checkDirectory();

        $checkState = true;

        if (version_compare(PHP_VERSION, '7.2', '<') === true) {
            $webVersion = '<span class="text-danger">'.PHP_VERSION.' [PHP版本要 >= 7.2]</span>';
            $checkState = false;
        } else $webVersion = PHP_VERSION;

        if (!extension_loaded('gd')) return $this->response->setContent($this->translator->translate('GD库未安装'));

        $fileGetContents= function_exists('file_get_contents');
        $filePutContents= function_exists('file_put_contents');
        $pdoOpen        = extension_loaded('pdo_mysql');
        $curlOpen       = extension_loaded('curl');
        $finfoOpen      = extension_loaded('fileinfo');
        $intlOpen       = extension_loaded('intl');
        $sslOpen        = extension_loaded('openssl');

        if (
            !$fileGetContents
            || !$filePutContents
            || !$pdoOpen
            || !$curlOpen
            || !$finfoOpen
            || !$intlOpen
            || !$sslOpen) $checkState = false;

        if ($checkState) $this->session->offsetSet('installStep', 2);

        return [
            'osVersion' => PHP_OS,
            'webVersion'=> $_SERVER['SERVER_SOFTWARE'],
            'phpVersion'=> $webVersion,
            'gdVersion' => gd_info()['GD Version'],

            'fileGetContents'   => $fileGetContents,
            'filePutContents'   => $filePutContents,
            'pdoOpen'           => $pdoOpen,
            'curlOpen'          => $curlOpen,
            'finfoOpen'         => $finfoOpen,
            'intlOpen'          => $intlOpen,
            'sslOpen'           => $sslOpen,

            'checkState'        => $checkState,

            'checkDirArray' => $checkDirArray
        ];
    }

    /**
     * 安装页面
     * @return InstallForm[]|\Laminas\Http\Response
     */
    public function installErpAction()
    {
        if (!$this->session->offsetExists('installStep') || !in_array($this->session->offsetGet('installStep'), [2])) return $this->redirect()->toRoute('erp-install');

        $localLanguage = $this->translator->getLocale();
        if(!file_exists('data/moduleData/System/TimezoneData/' . $localLanguage . '.php')) $localLanguage = 'zh_CN';
        $timeZone = include 'data/moduleData/System/TimezoneData/' . $localLanguage . '.php';

        $form = new InstallForm();
        $form->get('timeZone')->setValueOptions($timeZone);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->db = new Adapter([
                    'driver'    => 'Pdo',
                    'dsn'       => 'mysql:dbname='.$data['dbName'].';port='.$data['dbPort'].';host='.$data['dbHost'].';charset=utf8',
                    'username'  => $data['dbUser'],
                    'password'  => $data['dbPassword'],
                    'driver_options' => [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"
                    ]
                ]);
                //安装基础数据库信息
                $this->installBaseFileSql();
                //插入必要的数据信息
                $this->insertFileSql([
                    's' => [
                        '{shopName}',
                        '{timeZone}'
                    ],
                    't' => [
                        $data['shopName'],
                        $data['timeZone']
                    ]
                ]);
                //系统创始人信息添加
                $addAdminSql = "INSERT INTO dberp_admin (admin_id, admin_group_id, admin_name, admin_passwd, admin_email, admin_state, admin_add_time) VALUES(1, 1, '".$data['adminUser']."', '".Common::createAdminPassword($data['adminPassword'])."', '".$data['adminEmail']."', 1, ".time().")";
                $this->executeSql($addAdminSql);

                $writeFile = new PhpArray();
                $writeFile->setUseBracketArraySyntax(true);

                $configArray = Factory::fromFile('data/moduleData/System/erpConfig.php');
                $system = $this->db->createStatement("SELECT * FROM dberp_system WHERE sys_type NOT IN ('upload', 'customer')");
                $systemResult = $system->execute();
                if ($systemResult instanceof ResultInterface && $systemResult->isQueryResult()) {
                    $resultSet = new ResultSet();
                    $resultSet->initialize($systemResult);
                    foreach ($resultSet as $row) {
                        $configArray[$row->sys_type][$row->sys_name] = $row->sys_body;
                    }
                }
                $writeFile->toFile('data/moduleData/System/erpConfig.php', $configArray);

                $writeFile->toFile('data/erpDatabase.php', [
                    'host'     => $data['dbHost'],
                    'user'     => $data['dbUser'],
                    'password' => $data['dbPassword'],
                    'dbname'   => $data['dbName'],
                    'port'     => $data['dbPort'],
                    'charset'  => 'utf8mb4'
                ]);
                
                Common::opcacheReset();

                $this->session->offsetSet('installStep', 3);
                $this->session->offsetSet('installInfo', ['shopName' => $data['shopName'], 'adminUser' => $data['adminUser'], 'adminPassword' => $data['adminPassword']]);

                return $this->redirect()->toRoute('erp-install', ['action' => 'installFinish']);
            }
        }

        return ['form' => $form];
    }

    /**
     * 安装完成
     * @return array|\Laminas\Http\Response
     */
    public function installFinishAction()
    {
        if (!$this->session->offsetExists('installStep') || !in_array($this->session->offsetGet('installStep'), [3])) return $this->redirect()->toRoute('shop-install');

        file_put_contents('data/install.lock', 'lock');

        $installInfo = $this->session->offsetGet('installInfo');
        $this->session->offsetUnset('installStep');
        $this->session->offsetUnset('installInfo');

        $dirArray = scandir('data/cache/');
        foreach ($dirArray as $fileName) {
            if (!in_array($fileName, ['.', '..', '.gitkeep']) && !is_dir('data/cache/'.$fileName)) {
                if (stripos($fileName, 'cache.php') !== false) unlink('data/cache/'.$fileName);
            }
        }

        return $installInfo;
    }

    /**
     * 安装基础数据库
     * @return false
     */
    private function installBaseFileSql()
    {
        $sqlStr = file_get_contents('module/Install/data/install/DBERP.sql');
        if (empty($sqlStr)) return false;

        $sqlStr = str_replace(["\r\n", "\r"], "\n", $sqlStr);
        $sqlArray = explode(";\n", $sqlStr);
        foreach ($sqlArray as $sql) {
            $querySql = trim(str_replace("\n", '', $sql));
            if($querySql) {
                $query = $this->db->createStatement($querySql);
                $query->prepare();
                $query->execute();
            }
        }
    }

    private function insertFileSql(array $array): void
    {
        $sqlStr = file_get_contents('module/Install/data/install/insertData/insert.sql');

        if (empty($sqlStr)) {
            return;
        }
        if (!empty($array)) $sqlStr = str_replace($array['s'], $array['t'], $sqlStr);

        $sqlStr = str_replace(["\r\n", "\r"], "\n", $sqlStr);
        $sqlArray = explode(";\n", $sqlStr);
        foreach ($sqlArray as $sql) {
            $querySql = trim(str_replace("\n", '', $sql));
            if($querySql) {
                $query = $this->db->createStatement($querySql);
                $query->prepare();
                $query->execute();
            }
        }
    }

    /**
     * 执行某一句sql语句
     * @param $sql
     */
    private function executeSql($sql)
    {
        $dbQuery = $this->db->createStatement($sql);
        $dbQuery->prepare();
        $dbQuery->execute();
    }

    /**
     * 目录检测
     * @return array
     */
    private function checkDirectory()
    {
        $dirItem = [
            '../data/'                              => 'data/',
            '../data/cache/'                        => 'data/cache/',
            '../data/error/'                        => 'data/error/',
            '../data/moduleData/Package/'           => 'data/moduleData/Package/',
            '../data/moduleData/System/'            => 'data/moduleData/System/',

            '../data/moduleExtend.php'              => 'data/moduleExtend.php',
            '../data/erpVersion.php'                => 'data/erpVersion.php',
            '../data/erpDatabase.php'               => 'data/erpDatabase.php',

            '/upload/payable/'                      => 'public/upload/payable/',
            '/upload/receivable/'                   => 'public/upload/receivable/',
        ];

        $array = [];
        foreach ($dirItem as $key => $value) {
            if ((is_dir($value) || file_exists($value)) && is_writable($value)) $array[$key] = $value;
        }

        return ['dirItem' => $dirItem, 'checkDirItem' => $array];
    }
}