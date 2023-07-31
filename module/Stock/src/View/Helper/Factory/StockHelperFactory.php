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

namespace Stock\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Stock\View\Helper\StockHelper;
use Laminas\Mvc\I18n\Translator;

class StockHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StockHelper
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $translator    = $container->get(Translator::class);

        return new StockHelper($entityManager, $translator);
    }
}