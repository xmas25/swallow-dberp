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
use Stock\Controller\ExWarehouseController;
use Stock\Service\ExWarehouseOrderGoodsManager;
use Stock\Service\ExWarehouseOrderManager;

class ExWarehouseControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ExWarehouseController
    {
        $translator                     = $container->get(Translator::class);
        $entityManager                  = $container->get('doctrine.entitymanager.orm_default');
        $exWarehouseOrderManager        = $container->get(ExWarehouseOrderManager::class);
        $exWarehouseOrderGoodsManager   = $container->get(ExWarehouseOrderGoodsManager::class);

        return new ExWarehouseController($translator, $entityManager, $exWarehouseOrderManager, $exWarehouseOrderGoodsManager);
    }
}