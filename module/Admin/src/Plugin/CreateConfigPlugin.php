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
use Admin\Entity\System;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class CreateConfigPlugin extends AbstractPlugin
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 系统设置生成配置文件
     */
    public function createSystem()
    {
        $shopConfig = $this->entityManager->getRepository(System::class)->findBaseSystem();
        $configArray = [];
        foreach ($shopConfig as $configValue) {
            $configArray[$configValue->getSysType()][$configValue->getSysName()] = $configValue->getSysBody();
        }
        Common::writeConfigFile('config', $configArray);
    }

}