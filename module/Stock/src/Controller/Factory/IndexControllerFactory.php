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

namespace Stock\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Stock\Controller\IndexController;
use Stock\Service\OtherWarehouseOrderGoodsManager;
use Stock\Service\OtherWarehouseOrderManager;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndexController
    {
        $translator                     = $container->get(Translator::class);
        $entityManager                  = $container->get('doctrine.entitymanager.orm_default');
        $otherWarehouseOrderManager     = $container->get(OtherWarehouseOrderManager::class);
        $otherWarehouseOrderGoodsManager= $container->get(OtherWarehouseOrderGoodsManager::class);

        return new IndexController(
            $translator,
            $entityManager,
            $otherWarehouseOrderManager,
            $otherWarehouseOrderGoodsManager
        );
    }
}