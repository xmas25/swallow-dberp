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
use Stock\Entity\StockTransfer;
use Stock\Entity\StockTransferGoods;
use Stock\Form\StockTransferForm;
use Stock\Form\StockTransferGoodsForm;
use Stock\Service\StockTransferGoodsManager;
use Stock\Service\StockTransferManager;

/**
 * 库间调拨
 */
class StockTransferController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $stockTransferManager;
    private $stockTransferGoodsManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        StockTransferManager $stockTransferManager,
        StockTransferGoodsManager $stockTransferGoodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->stockTransferManager         = $stockTransferManager;
        $this->stockTransferGoodsManager    = $stockTransferGoodsManager;
    }

    /**
     * 库间调拨首页
     * @return array
     */
    public function indexAction(): array
    {
        $array= [];
        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];

        $query = $this->entityManager->getRepository(StockTransfer::class)->findStockTransferList($search);
        $array['stockTransferList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加库间调拨
     * @return array|\Laminas\Http\Response
     */
    public function addAction()
    {
        $goodsForm  = new StockTransferGoodsForm($this->entityManager);
        $form       = new StockTransferForm($this->entityManager);

        $form->get('transferOutWarehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());
        $form->get('transferInWarehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if ($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $stockTransferInfo = $this->stockTransferManager->addStockTransfer($data, $this->adminSession('admin_id'));
                    $this->stockTransferGoodsManager->addStockTransferGoods($goodsData, $stockTransferInfo->getTransferOutWarehouseId(), $stockTransferInfo->getTransferInWarehouseId(), $stockTransferInfo->getTransferId());

                    $this->entityManager->commit();

                    $message = $stockTransferInfo->getTransferSn() . $this->translator->translate('库间调拨添加成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('库间调拨'));
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('库间调拨添加失败！'));
                }
                return $this->redirect()->toRoute('stock-transfer');
            }
        } else $form->get('transferSn')->setValue($this->stockPlugin()->createStockTransferSn());

        return ['form' => $form, 'goodsForm' => $goodsForm];
    }

    /**
     * 查看调拨单详情
     * @return array|\Laminas\Http\Response
     */
    public function viewAction()
    {
        $stockTransferId    = (int) $this->params()->fromRoute('id', -1);
        $stockTransferInfo  = $this->entityManager->getRepository(StockTransfer::class)->findOneBy(['transferId' => $stockTransferId]);
        if($stockTransferInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库间调拨单不存在！'));
            return $this->redirect()->toRoute('stock-transfer');
        }

        $stockTransferGoods = $this->entityManager->getRepository(StockTransferGoods::class)->findBy(['transferId' => $stockTransferId], ['transferGoodsId' => 'ASC']);

        return ['transferInfo' => $stockTransferInfo, 'transferGoods' => $stockTransferGoods];
    }

    /**
     * 审核完成库间调拨
     * @return mixed
     */
    public function authPassStockTransferAction()
    {
        $transferId     = (int) $this->params()->fromRoute('id', -1);
        $transferInfo   = $this->entityManager->getRepository(StockTransfer::class)->findOneBy(['transferId' => $transferId]);
        if($transferInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库间调拨单不存在！'));
            return $this->adminCommon()->toReferer();
        }
        if($transferInfo->getTransferState() == 1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库间调拨单已经完成，无需重复处理！'));
            return $this->adminCommon()->toReferer();
        }

        $this->entityManager->beginTransaction();
        try {
            $this->stockTransferManager->updateStockTransferState(1, time(), $transferInfo);
            $this->getEventManager()->trigger('stock-transfer.update.post', $this, $transferInfo);

            $this->entityManager->commit();

            $message = $transferInfo->getTransferSn() . $this->translator->translate('库间调拨审核完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('库间调拨'));
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->flashMessenger()->addWarningMessage($this->translator->translate('审核调拨失败！'));
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 删除库间调拨单
     * @return mixed
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $stockTransferId    = (int) $this->params()->fromRoute('id', -1);
        $stockTransferInfo  = $this->entityManager->getRepository(StockTransfer::class)->findOneBy(['transferId' => $stockTransferId]);
        if($stockTransferInfo) {
            if($stockTransferInfo->getTransferState() == 1) $this->flashMessenger()->addWarningMessage($this->translator->translate('该库间调拨单已经完成，不能删除！'));
            else {
                $this->entityManager->beginTransaction();
                try {
                    $this->stockTransferManager->deleteStockTransfer($stockTransferInfo);
                    $this->stockTransferGoodsManager->deleteStockTransferIdGoods($stockTransferId);

                    $this->entityManager->commit();

                    $message = $stockTransferInfo->getTransferSn() . $this->translator->translate('库间调拨单删除成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('库间调拨'));
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('库间调拨单删除失败！'));
                }
            }
        } else $this->flashMessenger()->addWarningMessage($this->translator->translate('该库间调拨单不存在！'));

        return $this->adminCommon()->toReferer();
    }
}