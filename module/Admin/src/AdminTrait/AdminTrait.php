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

namespace Admin\AdminTrait;

use Admin\Data\Common;

trait AdminTrait
{
    /**
     * 在线安装更新处理服务集合
     * @param $packageInfo
     * @param $dataArray
     * @param $packageType
     * @return array|mixed|string[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function onlineOperationPackage($packageInfo, $dataArray, $packageType)
    {
        $packageName = '';
        $packageDownloadType = '';
        switch ($packageType) {
            case 'update':      //系统更新
                $packageName = 'packageName';
                $packageDownloadType = 'downloadErpUpdatePackage';
                break;
            case 'plugin':      //插件安装
                $packageName = 'pluginInstallPackage';
                $packageDownloadType = 'downloadErpPluginPackage';
                break;
            case 'updatePlugin'://插件更新
                $packageName = 'pluginPackage';
                $packageDownloadType = 'downloadErpPluginUpdatePackage';
                break;
        }

        $packagePath = 'data/moduleData/Package';
        $packageDirName = str_replace([".zip", ".ZIP", ".tar.gz"], '', $packageInfo[$packageName]);
        $packageZipFile = $packagePath . '/' . $packageInfo[$packageName];
        $sourcePath = $packagePath . '/' . $packageDirName;

        $downState = $this->adminCommon()->dberpDownloadPackage($packageDownloadType, $dataArray, $packageZipFile);
        if (is_string($downState) && $downState == 'keyError') return ['state' => 'false', 'message' => $this->translator->translate('您的网址无法从公网正常访问，不能进行继续操作。')];
        if (!$downState) return ['state' => 'false', 'message' => $this->translator->translate('包下载失败，无法更新。')];
        if (md5_file($packageZipFile) != strtolower($packageInfo['packageMd5'])) {
            @unlink($packageZipFile);
            return ['state' => 'false', 'message' => $this->translator->translate('下载的包与原始包不匹配，无法更新。')];
        }

        Common::decompressPackage($packageZipFile, $packagePath);

        //检查权限
        $allUpdateFiles = Common::getAllFile($sourcePath);
        if (empty($allUpdateFiles)) return ['state' => 'false', 'message' => $this->translator->translate('无可安装文件。')];
        $checkString = '';
        foreach ($allUpdateFiles as $updateFile) {
            $coverFile = ltrim(str_replace($sourcePath, '', $updateFile), DIRECTORY_SEPARATOR);
            $dirPath = dirname($coverFile);
            if (file_exists($coverFile)) {
                if (!is_writable($coverFile)) $checkString .= $coverFile . '&nbsp;[<span class="text-red">' . $this->translator->translate('无写入权限') . '</span>]<br>';
            } else {
                if (!is_dir($dirPath)) @mkdir($dirPath, 0777, true);
                if (!is_writable($dirPath)) $checkString .= $dirPath . '&nbsp;[<span class="text-red">' . $this->translator->translate('无写入权限') . '</span>]<br>';
            }
        }
        if (!empty($checkString)) return ['state' => 'false', 'message' => $checkString];

        //进行覆盖
        foreach ($allUpdateFiles as $updateFile) {
            $coverFile = ltrim(str_replace($sourcePath, '', $updateFile), DIRECTORY_SEPARATOR);
            if (!copy($updateFile, $coverFile)) return ['state' => 'false', 'message' => sprintf($this->translator->translate('无法正常安装，请检查%s对应的目录是否有相关权限'), $coverFile)];
            @chmod($coverFile, 0755);
        }

        //系统更新包更新
        if ($packageType == 'update' && file_exists($sourcePath . '/sql/update.sql')) {
            $updateSql = str_replace(["\r\n", "\r"], "\n", file_get_contents($sourcePath . '/sql/update.sql'));
            $this->entityManager->getConnection()->prepare($updateSql)->execute();
        }
        //系统插件安装
        if ($packageType == 'plugin' && file_exists($sourcePath . '/module/Plugin/' . $dataArray['pluginCode'] . '/pluginSql/table.sql')) {
            $updateSql = str_replace(["\r\n", "\r"], "", file_get_contents($sourcePath . '/module/Plugin/' . $dataArray['pluginCode'] . '/pluginSql/table.sql'));
            $this->entityManager->getConnection()->prepare($updateSql)->execute();
        }
        //系统插件更新
        if ($packageType == 'updatePlugin' && file_exists($sourcePath . '/module/Plugin/' . $dataArray['pluginCode'] . '/sql/update.sql')) {
            $updateSql = str_replace(["\r\n", "\r"], "", file_get_contents($sourcePath . '/module/Plugin/' . $dataArray['pluginCode'] . '/sql/update.sql'));
            $this->entityManager->getConnection()->prepare($updateSql)->execute();
        }

        //删除包及文件
        @unlink($packageZipFile);
        Common::deleteDirAndFile($sourcePath);

        return $packageInfo;
    }
}