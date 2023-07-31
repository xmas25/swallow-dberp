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

namespace Store\Event;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\WarehouseOrderGoods;
use Stock\Entity\ExWarehouseOrderGoods;
use Stock\Entity\OtherWarehouseOrderGoods;
use Stock\Entity\StockCheckGoods;
use Stock\Entity\StockTransferGoods;
use Stock\Service\StockTransferGoodsManager;
use Stock\Service\StockTransferManager;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;

class Listener implements ListenerAggregateInterface
{
    protected $listeners = [];

    private $entityManager;
    private $goodsManager;
    private $warehouseGoodsManager;
    private $stockTransferGoodsManager;
    private $stockTransferManager;

    public function __construct(
        EntityManager   $entityManager,
        GoodsManager    $goodsManager,
        WarehouseGoodsManager $warehouseGoodsManager,
        StockTransferGoodsManager   $stockTransferGoodsManager,
        StockTransferManager        $stockTransferManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->goodsManager     = $goodsManager;
        $this->warehouseGoodsManager = $warehouseGoodsManager;
        $this->stockTransferGoodsManager= $stockTransferGoodsManager;
        $this->stockTransferManager     = $stockTransferManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $shareEvents = $events->getSharedManager();

        //商品价格与库存更新，验收采购入库，直接入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.add.post', [$this, 'onUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //商品价格与库存更新，待入库单入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.insert.post', [$this, 'onUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //库存-其他入库，入库处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\IndexController', 'other-warehouse-order.insert.post', [$this, 'onOtherUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //库存-其他出库，出库处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\ExWarehouseController', 'ex-warehouse-order.out.post', [$this, 'onExUpdateWarehouseGoodsStock']
        );

        //库存-盘点，确认处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\StockCheckController', 'stock-check.update.post', [$this, 'onStockCheckUpdateGoodsStockAndWarehouseGoods']
        );

        //库存-调拨，审核确认处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\StockTransferController', 'stock-transfer.update.post', [$this, 'onStockTransferUpdateWarehouseGoods']
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    /**
     * 更新商品的价格、商品库存、仓库库存
     * @param Event $event
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onUpdateGoodsPriceAndStockAndWarehouseGoods(Event $event)
    {
        $warehouseOrder = $event->getParams();

        if($warehouseOrder->getWarehouseOrderState() == 3) {//只有当入库时，才会进行处理
            $orderGoods = $this->entityManager->getRepository(WarehouseOrderGoods::class)->findBy(['warehouseOrderId' => $warehouseOrder->getWarehouseOrderId()]);
            if($orderGoods != null) {
                foreach ($orderGoods as $goodsObject) {
                    if($goodsObject->getWarehouseGoodsBuyNum() <= 0) continue;

                    $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsObject->getGoodsId());
                    if($goodsInfo) {
                        $data = [
                            'goodsStock' => $goodsInfo->getGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(),
                            'goodsPrice' => $goodsObject->getWarehouseGoodsPrice()
                        ];
                        //先在仓库中写入
                        $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                        if($warehouseGoods == null) $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $warehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $goodsObject->getWarehouseGoodsBuyNum()]);
                        else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock()+$goodsObject->getWarehouseGoodsBuyNum(), $warehouseGoods);
                        //商品中更新
                        $this->goodsManager->updateGoodsPriceAndStock($data, $goodsInfo);
                    }
                }
            }
        }
    }

    /**
     * 更新商品的价格、商品库存、仓库库存(其他入库)
     * @param Event $event
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onOtherUpdateGoodsPriceAndStockAndWarehouseGoods(Event $event)
    {
        $otherWarehouseOrder = $event->getParams();
        $otherOrderGoods = $this->entityManager->getRepository(OtherWarehouseOrderGoods::class)->findBy(['otherWarehouseOrderId' => $otherWarehouseOrder->getOtherWarehouseOrderId()]);
        if($otherOrderGoods != null) {
            foreach ($otherOrderGoods as $goodsObject) {
                if($goodsObject->getWarehouseGoodsBuyNum() <= 0) continue;

                $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsObject->getGoodsId()]);
                if($goodsInfo) {
                    $data = [
                        'goodsStock' => $goodsInfo->getGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(),
                        //'goodsPrice' => $goodsObject->getWarehouseGoodsPrice()
                        'goodsPrice' => $goodsInfo->getGoodsPrice() //在其他入库中，不将价格更新
                    ];
                    $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $otherWarehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                    if($warehouseGoods == null) {
                        $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $otherWarehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $goodsObject->getWarehouseGoodsBuyNum()]);
                    } else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(), $warehouseGoods);

                    //商品中更新
                    $this->goodsManager->updateGoodsPriceAndStock($data, $goodsInfo);
                }
            }
        }
    }

    /**
     * 更新商品库存、仓库库存(其他出库)
     * @param Event $event
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onExUpdateWarehouseGoodsStock(Event $event)
    {
        $exWarehouseOrder = $event->getParams();
        $exOrderGoods = $this->entityManager->getRepository(ExWarehouseOrderGoods::class)->findBy(['exWarehouseOrderId' => $exWarehouseOrder->getExWarehouseOrderId()]);
        if ($exOrderGoods != null) {
            foreach ($exOrderGoods as $goodsObject) {
                if ($goodsObject->getWarehouseGoodsExNum() <= 0) continue;

                $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsObject->getGoodsId()]);
                if ($goodsInfo) {
                    $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $exWarehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                    if ($warehouseGoods) {
                        $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() - $goodsObject->getWarehouseGoodsExNum(), $warehouseGoods);
                        $this->goodsManager->updateGoodsStock($goodsInfo->getGoodsStock() - $goodsObject->getWarehouseGoodsExNum(), $goodsInfo);
                    }
                }
            }
        }
    }

    /**
     * 更新库存（库存盘点）
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onStockCheckUpdateGoodsStockAndWarehouseGoods(Event $event)
    {
        $stockCheckInfo = $event->getParams();
        $stockCheckGoods= $this->entityManager->getRepository(StockCheckGoods::class)->findBy(['stockCheckId' => $stockCheckInfo->getStockCheckId()]);
        foreach ($stockCheckGoods as $stockGoods) {
            if($stockGoods->getStockCheckAftGoodsNum() < 0) continue;

            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $stockGoods->getGoodsId()]);
            if($goodsInfo) {
                $addGoodsStock  = $stockGoods->getStockCheckAftGoodsNum() - $stockGoods->getStockCheckPreGoodsNum();
                $goodsStock     = $goodsInfo->getGoodsStock() + $addGoodsStock;

                $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $stockCheckInfo->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                if($warehouseGoods == null) {
                    $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $stockCheckInfo->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $addGoodsStock]);
                } else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() + $addGoodsStock, $warehouseGoods);

                $this->goodsManager->updateGoodsStock($goodsStock, $goodsInfo);
            }
        }
    }

    /**
     * 更新仓库库存（库间调拨）
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onStockTransferUpdateWarehouseGoods(Event $event)
    {
        $transferInfo = $event->getParams();
        $transferGoods= $this->entityManager->getRepository(StockTransferGoods::class)->findBy(['transferId' => $transferInfo->getTransferId()]);
        $transferState= true;
        foreach ($transferGoods as $goodsValue) {
            if($goodsValue->getTransferGoodsNum() <= 0) continue;

            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsValue->getGoodsId()]);
            if($goodsInfo) {
                $outWarehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $goodsValue->getOutWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                if($outWarehouseGoods && $outWarehouseGoods->getWarehouseGoodsStock() >= $goodsValue->getTransferGoodsNum()) {
                    $this->warehouseGoodsManager->updateWarehouseGoodsStock($outWarehouseGoods->getWarehouseGoodsStock() - $goodsValue->getTransferGoodsNum(), $outWarehouseGoods);
                    $inWarehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $goodsValue->getInWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                    if($inWarehouseGoods) $this->warehouseGoodsManager->updateWarehouseGoodsStock($inWarehouseGoods->getWarehouseGoodsStock() + $goodsValue->getTransferGoodsNum(), $inWarehouseGoods);
                    else $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $goodsValue->getInWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $goodsValue->getTransferGoodsNum()]);

                    $this->stockTransferGoodsManager->updateStockTransferGoodsState(1, $goodsValue);
                } else {
                    $this->stockTransferGoodsManager->updateStockTransferGoodsState(2, $goodsValue);
                    $transferState = false;
                }
            }
        }
        if(!$transferState) $this->stockTransferManager->updateStockTransferState(2, time(), $transferInfo);
    }
}