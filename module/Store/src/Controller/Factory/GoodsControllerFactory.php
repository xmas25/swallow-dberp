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

namespace Store\Controller\Factory;

use Interop\Container\ContainerInterface;
use Store\Controller\GoodsController;
use Store\Service\GoodsCategoryManager;
use Store\Service\GoodsCustomManager;
use Store\Service\GoodsManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class GoodsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GoodsController
    {
        $translator         = $container->get(Translator::class);
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $goodsCategoryManager = $container->get(GoodsCategoryManager::class);
        $goodsManager       = $container->get(GoodsManager::class);
        $goodsCustomManager = $container->get(GoodsCustomManager::class);

        return new GoodsController($translator, $entityManager, $goodsCategoryManager, $goodsManager, $goodsCustomManager);
    }
}