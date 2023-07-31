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

namespace Stock\Controller;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Stock\Entity\ExWarehouseOrder;
use Stock\Entity\ExWarehouseOrderGoods;
use Stock\Form\ExOrderSearchForm;
use Stock\Form\ExWarehouseOrderForm;
use Stock\Form\ExWarehouseOrderGoodsForm;
use Stock\Service\ExWarehouseOrderGoodsManager;
use Stock\Service\ExWarehouseOrderManager;

class ExWarehouseController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $exWarehouseOrderManager;
    private $exWarehouseOrderGoodsManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        ExWarehouseOrderManager $exWarehouseOrderManager,
        ExWarehouseOrderGoodsManager $exWarehouseOrderGoodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->exWarehouseOrderManager = $exWarehouseOrderManager;
        $this->exWarehouseOrderGoodsManager = $exWarehouseOrderGoodsManager;
    }

    /**
     * 其他出库列表
     * @return array
     */
    public function indexAction(): array
    {
        $array = [];

        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new ExOrderSearchForm();
        $searchForm->get('warehouse_id')->setValueOptions($this->storeCommon()->warehouseListOptions());
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;
        $query = $this->entityManager->getRepository(ExWarehouseOrder::class)->findExWarehouseOrderList($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加其他出库
     * @return array|\Laminas\Http\Response
     */
    public function addAction()
    {
        $goodsForm  = new ExWarehouseOrderGoodsForm($this->entityManager);
        $form       = new ExWarehouseOrderForm($this->entityManager);

        $form->get('warehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if ($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $exWarehouseOrder = $this->exWarehouseOrderManager->addExWarehouseOrder($data, $goodsData,  $this->adminSession('admin_id'));
                    $this->exWarehouseOrderGoodsManager->addExWarehouseOrderGoods($goodsData, $data['warehouseId'], $exWarehouseOrder->getExWarehouseOrderId());

                    $this->getEventManager()->trigger('ex-warehouse-order.out.post', $this, $exWarehouseOrder);

                    $this->entityManager->commit();

                    $message = $exWarehouseOrder->getExWarehouseOrderSn() . $this->translator->translate('其他出库成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('其他出库'));
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('其他出库失败！'));
                }
                return $this->redirect()->toRoute('stock-ex');
            }
        } else $form->get('exWarehouseOrderSn')->setValue($this->stockPlugin()->createExWarehouseOrderSn());

        return ['form' => $form, 'goodsForm' => $goodsForm];
    }

    /**
     * 查看其他出库订单信息
     * @return array|\Laminas\Http\Response
     */
    public function viewAction()
    {
        $exWarehouseOrderId = (int) $this->params()->fromRoute('id', -1);
        $exWarehouseOrderInfo = $this->entityManager->getRepository(ExWarehouseOrder::class)->findOneBy(['exWarehouseOrderId' => $exWarehouseOrderId]);
        if ($exWarehouseOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该出库单不存在！'));
            return $this->redirect()->toRoute('stock-ex');
        }

        $orderGoods = $this->entityManager->getRepository(ExWarehouseOrderGoods::class)->findBy(['exWarehouseOrderId' => $exWarehouseOrderId]);

        return ['exWarehouseOrder' => $exWarehouseOrderInfo, 'orderGoods' => $orderGoods];
    }
}