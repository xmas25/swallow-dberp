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

namespace Admin\Plugin\Factory;

use Admin\Data\Config;
use Admin\Plugin\CreateConfigPlugin;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CreateConfigPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CreateConfigPlugin
    {
        $entityManager = $container->get(Config::ERP_DATABASE_MANAGER);

        return new CreateConfigPlugin($entityManager);
    }
}