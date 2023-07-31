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

namespace Admin\Data;

use Laminas\Config\Factory;
use Laminas\Config\Writer\PhpArray;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Filter\Decompress;
use Laminas\Mvc\I18n\Translator;

class Common
{
    /**
     * 生成的配置文件的后缀名
     * @var string
     */
    private static $configFileSuffix = '.php';

    /**
     * 配置文件生成方法
     * @param $key
     * @param array $data
     * @return bool
     */
    public static function writeConfigFile($key, array $data): bool
    {
        $writeFile = new PhpArray();
        $writeFile->setUseBracketArraySyntax(true);

        $configFile = self::configFile($key);
        $writeFile->toFile($configFile, $data);

        //如配置文件是php文件且开启了opcache 则设置该缓存失效
        if(self::$configFileSuffix == '.php') self::opcacheInvalidate($configFile);

        return true;
    }

    /**
     * 配置文件读取方法
     * @param $key
     * @return array|mixed|\Laminas\Config\Config
     */
    public static function readConfigFile($key)
    {
        if(self::$configFileSuffix == '.php') {
            $includeFile = self::configFile($key);
            if (file_exists($includeFile)) return include $includeFile;
            return [];
        }

        return Factory::fromFile(self::configFile($key));
    }

    /**
     * 配置文件路径
     * @param $key
     * @return string
     */
    private static function configFile($key)
    {
        $fileArray = [
            'config'    => 'data/moduleData/System/erpConfig',      //系统配置
            'erpService'=> 'data/moduleData/System/erpService',     //dberp服务调用

            'pluginModule'  => 'data/moduleData/Plugin/plugin.module',//插件的module
            'moduleExtend'  => 'data/moduleExtend',//将插件动态注册
        ];
        return $fileArray[$key] . self::$configFileSuffix;
    }

    /**
     * 单个配置信息获取获取
     * @param $configKey
     * @param string $typeConfig
     * @return mixed|string[]|null
     */
    public static function configValue($configKey, string $typeConfig = 'upload')
    {
        $config = self::readConfigFile($typeConfig);

        if($configKey == 'upload_image_max') {//图片上传大小限制
            return ['min' => '1kB', 'max' => number_format($config[$configKey]/1024, 2) . 'MB'];
        }

        return isset($config[$configKey]) ? $config[$configKey] : null;
    }

    /**
     * 获取erp.json并解析
     * @param string $strName
     * @return array|\Laminas\Config\Config|mixed|string
     */
    public static function erpJson(string $strName = '')
    {
        $file = 'data/moduleData/System/erp.json';
        if (file_exists($file)) {
            $dbshop = Factory::fromFile($file);
            if (!empty($strName)) return $dbshop[$strName]??'';
            else return $dbshop;
        }
        return !empty($strName) ? '' : [];
    }

    /**
     * 获取opcache状态信息
     * @param $sign
     * @return bool|mixed
     */
    public static function opcacheStatus($sign)
    {
        if(function_exists('opcache_get_status')) {
            $status = opcache_get_status();
            if(isset($status[$sign])) return $status[$sign];
        }
        return false;
    }

    /**
     * 重置或清除整个字节码缓存数据
     * @return bool
     */
    public static function opcacheReset(): bool
    {
        if(self::opcacheStatus('opcache_enabled') && function_exists('opcache_reset')) return opcache_reset();
        return false;
    }

    /**
     * 指定某脚本文件字节码缓存失效
     * @param $script
     * @return bool
     */
    public static function opcacheInvalidate($script): bool
    {
        if(self::opcacheStatus('opcache_enabled') && function_exists('opcache_invalidate')) {
            return opcache_invalidate($script, true);
        }
        return false;
    }

    /**
     * 无需运行，就可以编译并缓存脚本
     * 该方法可以用于在不用运行某个 PHP 脚本的情况下，编译该 PHP 脚本并将其添加到字节码缓存中去。 该函数可用于在 Web 服务器重启之后初始化缓存，以供后续请求调用。
     * @param $file
     * @return bool
     */
    public static function opcacheCompile($file): bool
    {
        if(self::opcacheStatus('opcache_enabled') && function_exists('opcache_compile_file')) return opcache_compile_file($file);
        return false;
    }

    /**
     * 状态(通用)
     * @param Translator $translator
     * @return array
     */
    public static function state(Translator $translator): array
    {
        return [1 => $translator->translate('启用'), 0 => $translator->translate('禁用')];
    }

    /**
     * 绑定应用的种类
     * @param Translator $translator
     * @return array
     */
    public static function appType(Translator $translator): array
    {
        return [
            'dbshop' => $translator->translate('DBShop商城系统'),
            'dbcart' => $translator->translate('DBCart多语言商城系统'),
            'other' => $translator->translate('其他系统')
        ];
    }

    /**
     * 采购订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function purchaseOrderState(Translator $translator, int $style = 1): array
    {
        return [
            -5  => $translator->translate('已退货'),
            -1  => $translator->translate('退货'),
            0   => $style == 1 ? $translator->translate('未审核') : '<strong>'.$translator->translate('未审核').'</strong>',
            1   => $style == 1 ? $translator->translate('已审核') : '<strong style="color: green;">'.$translator->translate('已审核').'</strong>',
            2   => $style == 1 ? $translator->translate('待入库') : '<strong>'.$translator->translate('待入库').'</strong>',
            3   => $style == 1 ? $translator->translate('已入库') : '<strong style="color: green;">'.$translator->translate('已入库').'</strong>'
        ];
    }

    /**
     * 采购退货单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function purchaseOrderReturnState(Translator $translator, int $style = 1): array
    {
        return [
            -1  => $translator->translate('退货中'),
            -5  => $style == 1 ? $translator->translate('已退货') : '<strong class="text-green">'.$translator->translate('已退货').'</strong>'
        ];
    }

    /**
     * 付款方式
     * @param Translator $translator
     * @param string $topName
     * @return array
     */
    public static function payment(Translator $translator, string $topName = ''): array
    {
        return [
            ''          => empty($topName) ? $translator->translate('付款方式') : $topName,
            'payable'   => $translator->translate('应付账款'),
            'cashPay'   => $translator->translate('现金付款'),
            'advancePay'=> $translator->translate('预付款')
        ];
    }

    /**
     * 销售订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function salesOrderState(Translator $translator, int $style = 1): array
    {
        return [
            -5  => $translator->translate('已退货'),
            -1  => $translator->translate('退货'),
            0   => $style == 1 ? $translator->translate('待确认') : '<strong>'.$translator->translate('待确认').'</strong>',
            1   => $style == 1 ? $translator->translate('已确认') : '<strong class="text-blue">'.$translator->translate('已确认').'</strong>',
            6   => $style == 1 ? $translator->translate('发货出库') : '<strong>'.$translator->translate('发货出库').'</strong>',
            12  => $style == 1 ? $translator->translate('确认收货') : '<strong class="text-green">'.$translator->translate('确认收货').'</strong>',
        ];
    }

    /**
     * 退货订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function salesOrderReturnState(Translator $translator, int $style = 1): array
    {
        return [
            -1  => $translator->translate('退货中'),
            -5  => $style == 1 ? $translator->translate('已退货') : '<strong class="text-green">'.$translator->translate('已退货').'</strong>'
        ];
    }

    /**
     * 收款方式
     * @param Translator $translator
     * @param string $topName
     * @return array
     */
    public static function receivable(Translator $translator, string $topName = ''): array
    {
        return [
            '' => (empty($topName) ? $translator->translate('收款方式') : $topName),
            'receivable'=> $translator->translate('应收账款'),
            'cashPay'   => $translator->translate('现金收款'),
            'prePay'    => $translator->translate('预存款')
        ];
    }

    /**
     * 入库状态
     * @param Translator $translator
     * @return array
     */
    public static function warehouseOrderState(Translator $translator): array
    {
        return [
            2 => $translator->translate('验货完成等待入库'),
            3 => $translator->translate('验货完成直接入库')
        ];
    }

    /**
     * 是否有退货
     * @param Translator $translator
     * @return array
     */
    public static function existReturn(Translator $translator): array
    {
        return [
            0 => $translator->translate('无'),
            1 => $translator->translate('有')
        ];
    }

    /**
     * 第三方商城订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function shopOrderState(Translator $translator, int $style = 1): array
    {
        return [
            0 => $translator->translate('已取消'),
            10 => $translator->translate('待付款'),
            15 => $translator->translate('付款中'),
            20 => $translator->translate('已付款'),
            30 => $translator->translate('待发货'),
            40 => $translator->translate('已发货'),
            60 => $translator->translate('订单完成')
        ];
    }

    /**
     * 库存盘点状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function StockCheckState(Translator $translator, int $style = 1): array
    {
        return [
            1  => $translator->translate('已盘点'),
            2  => $style == 1 ? $translator->translate('未盘点') : '<strong class="text-danger">'.$translator->translate('未盘点').'</strong>'
        ];
    }

    /**
     * 商城订单配货状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function distributionState(Translator $translator, int $style = 1): array
    {
        return [
            -1  => $translator->translate('缺货'),
            3   => $style == 1 ? $translator->translate('未匹配') : '<strong class="text-danger">'.$translator->translate('未匹配').'</strong>',
            4   => $translator->translate('已匹配'),
            6   => $translator->translate('已发货'),
            12  => $translator->translate('已确认收货')
        ];
    }

    /**
     * 库间调拨状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function stockTransferState(Translator $translator, int $style = 1): array
    {
        return [
            0  => $translator->translate('待调拨'),
            1  => $style == 1 ? $translator->translate('已调拨') : '<strong class="text-green">'.$translator->translate('已调拨').'</strong>',
            2  => $style == 1 ? $translator->translate('部分已调拨') : '<strong class="text-danger">'.$translator->translate('部分已调拨').'</strong>'
        ];
    }

    /**
     * 库存调拨商品状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function transferGoodsState(Translator $translator, int $style = 1): array
    {
        return [
            0  => $translator->translate('未调拨'),
            1  => $style == 1 ? $translator->translate('已调拨') : '<strong class="text-green">'.$translator->translate('已调拨').'</strong>',
            2  => $style == 1 ? $translator->translate('库存不足，未调拨') : '<strong class="text-danger">'.$translator->translate('库存不足，未调拨').'</strong>'
        ];
    }

    /**
     * 列出目录下的所有文件，包括子目录文件,不包含sql目录
     * @param $dirName
     * @return array
     */
    public static function getAllFile($dirName)
    {
        $dirName    = str_replace('..', '', rtrim($dirName, '/\\'));
        $fileArray  = [];
        if (is_dir($dirName)) {
            $dh = scandir($dirName);
            foreach ($dh as $file) {
                if (!in_array($file, ['.', '..', 'sql', '.DS_Store'])) {
                    $path = $dirName . DIRECTORY_SEPARATOR . $file;
                    if (!is_dir($path)) $fileArray[] = $path;
                    $fileArray = array_merge($fileArray, self::getAllFile($path));
                }
            }
        }
        return $fileArray;
    }

    /**
     * 删除目录及目录下的文件
     * @param $dirName
     * @param bool $delDir
     */
    public static function deleteDirAndFile($dirName, bool $delDir = true)
    {
        $dirName = str_replace('..', '', rtrim($dirName, '/\\'));
        if (is_dir($dirName)) {
            $dh = scandir($dirName);
            foreach ($dh as $file) {
                if (!in_array($file, ['.', '..', '.DS_Store'])) {
                    $fullPath = $dirName . DIRECTORY_SEPARATOR . $file;
                    if (!is_dir($fullPath)) @unlink($fullPath);
                    else self::deleteDirAndFile($fullPath);
                }
            }
            if ($delDir) @rmdir($dirName);
        }
    }

    /**
     * 解压压缩包
     * @param $packageFile
     * @param $toPath
     */
    public static function decompressPackage($packageFile, $toPath)
    {
        $ex = substr($packageFile, -4);
        switch ($ex) {
            case '.zip':
            case '.ZIP':
                $decompress = new Decompress(['adapter' => 'Zip', 'options' => ['target' => $toPath]]);
                $decompress->filter($packageFile);
                break;
            case 'r.gz':
                $decompress = new \PharData($packageFile);
                $decompress->extractTo($toPath, null, true);
                break;
        }
    }

    /**
     * 检查某个目录的所有文件，生成copy数组文件和删除数组文件，目前用于插件内需复制的目录
     * 用法如 Common::scanDirFileCopyArray('PluginModuleManagement/copyFile/public', '/copyFile/public', 'public');
     * @param $path
     * @param $copyPath
     * @param $copyToPath
     * @return void
     */
    public static function scanDirFileCopyArray($path, $copyPath, $copyToPath)
    {
        $fileArray  = self::getAllFile('module/Plugin/' . $path);
        $copyArray  = [];
        $delArray   = [];
        if (is_array($fileArray) && !empty($fileArray)) {
            foreach ($fileArray as $file) {
                $file       = str_replace(['module/Plugin/' . $path, '\\'], ['', '/'], $file);
                $copyArray[]= ['copy' => $copyPath . $file, 'copyTo' => $copyToPath . $file];
                $delArray[] = $copyToPath . $file;
            }
        }

        $writeFile = new PhpArray();
        $writeFile->setUseBracketArraySyntax(true);

        $writeFile->toFile('module/Plugin/' . $path . '/../copyFile.php', $copyArray);
        $writeFile->toFile('module/Plugin/' . $path . '/../delFile.php', $delArray);
    }

    /**
     * 图片删除
     * @param $image
     * @return bool
     */
    public static function deleteImages($image)
    {
        if(empty($image)) return false;
        $imageArray = is_array($image) ? $image : [$image];

        foreach ($imageArray as $imageValue) {
            if (stripos($imageValue, 'http') !== false) continue;

            $imagePath = dirname($imageValue);
            $imagePath = empty($imagePath) ? '' : str_replace('.', '', $imagePath) . '/';
            $imageName = basename($imageValue);
            $imageFile = getcwd() . '/public/' . $imagePath . $imageName;
            if(file_exists($imageFile) && !is_dir($imageFile)) unlink($imageFile);
        }
        return true;
    }

    /**
     * 高精度计算
     * @param $m
     * @param $n
     * @param string $type
     * @param int $scale
     * @return string|null
     */
    public static function calculation($m, $n, string $type, int $scale = 0)
    {
        $num = 0;
        switch ($type) {
            case 'add'://加
            case '+':
                $num = bcadd($m, $n, $scale);
                break;
            case 'sub'://减
            case '-':
                $num = bcsub($m, $n, $scale);
                break;
            case 'mul'://乘
            case '*':
                $num = bcmul($m, $n, $scale);
                break;
            case 'div'://除
            case '/':
                $num = bcdiv($m, $n, $scale);
                break;
        }
        return $num;
    }

    /**
     * 管理员密码生成
     * @param $password
     * @return string
     */
    public static function createAdminPassword($password)
    {
        $bcPassword = new Bcrypt();
        return $bcPassword->create($password);
    }

    /**
     * 验证管理员密码
     * @param $password
     * @param $adminPassword
     * @return bool
     */
    public static function verifyAdminPassword($password, $adminPassword)
    {
        $bcPassword = new Bcrypt();
        return $bcPassword->verify($password, $adminPassword);
    }

    /**
     * 判断是否为移动端
     * @return bool
     */
    public static function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            // 找不到为false,否则为true
            return true;
        }
        //判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientKeywords = [
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
                'MicroMessenger'
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientKeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}