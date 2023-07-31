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

namespace Api\Controller;

use Admin\Entity\App;
use Api\Form\ApiForm;
use Api\Form\OrderDeliveryForm;
use Api\Form\OrderSnForm;
use Api\Form\OrderTimeForm;
use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrder;
use Shop\Form\ShopOrderDeliveryAddressForm;
use Shop\Form\ShopOrderForm;
use Shop\Form\ShopOrderGoodsForm;
use Shop\Service\ShopOrderDeliveryAddressManager;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\JsonModel;
use Laminas\Crypt\BlockCipher;

class IndexController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $shopOrderManager;
    private $shopOrderGoodsManager;
    private $shopOrderDeliveryAddressManager;

    private $dataArray;
    private $appInfo = null;
    private $appId;

    //DBShop、DBCart商城Action
    private $dbshopActionArray = [
        'addOrder',
        'cancelOrder',
        'deleteOrder',
        'dbshop3PaymentOrder',
        'paymentOrder',
        'dbshop3DeliverOrder',
        'deliverOrder',
        'finishOrder',
    ];
    //其他商城系统Action
    private $otherActionArray = [
        'otherAddOrder',
        'otherCancelOrder',
        'otherDeleteOrder',
        'otherPaymentOrder',
        'otherDeliverOrder',
        'otherFinishOrder',
    ];

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        ShopOrderManager $shopOrderManager,
        ShopOrderGoodsManager $shopOrderGoodsManager,
        ShopOrderDeliveryAddressManager $shopOrderDeliveryAddressManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->shopOrderManager = $shopOrderManager;
        $this->shopOrderGoodsManager = $shopOrderGoodsManager;
        $this->shopOrderDeliveryAddressManager = $shopOrderDeliveryAddressManager;
    }

    /**
     * 这是一个公共入口
     * @return JsonModel|\Laminas\View\Model\ViewModel
     */
    public function indexAction()
    {
        $returnArray = ['code' => 404, 'status' => 'error', 'message' => $this->translator->translate('该账户不存在'), 'result' => []];

        if($this->getRequest()->isPost()) {
            $form = new ApiForm();
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data   = $form->getData();
                $this->appInfo= $this->entityManager->getRepository(App::class)->findOneBy(['appAccessId' => $data['appId'], 'appState' => 1]);
                if($this->appInfo == null) {
                    return new JsonModel($returnArray);
                }
                $this->appId = $this->appInfo->getAppId();
                if (in_array($this->appInfo->getAppType(), ['dbcart', 'dbshop'])) {//dbshop、dbcart商城系统
                    $blockCipher = BlockCipher::factory('openssl');
                    $blockCipher->setKey($this->appInfo->getAppAccessSecret());
                    $dataStr     = $blockCipher->decrypt($data['dataStr']);
                    $actionArray = $this->dbshopActionArray;
                } else {//其他商城系统
                    if (empty($data['sign'])) return new JsonModel($returnArray);
                    if ($data['sign'] != md5($data['dataStr'] . $this->appInfo->getAppAccessSecret())) {
                        $returnArray['message'] = $this->translator->translate('sign不一致');
                        return new JsonModel($returnArray);
                    }
                    $dataStr     = $data['dataStr'];
                    $actionArray = $this->otherActionArray;
                }

                if($dataStr
                    && in_array($data['action'], $actionArray)) {
                    $action = $data['action'];
                    $this->dataArray = Json::decode($dataStr, Json::TYPE_ARRAY);

                    $result = $this->$action();
                    return new JsonModel([
                        'code'      => 200,
                        'status'    => 'success',
                        'message'   => '',
                        'result'    => $result
                    ]);
                }
            }
        }

        return new JsonModel($returnArray);
    }

    /**
     * 添加订单
     * @return mixed
     */
    private function addOrder()
    {
        $orderForm = new ShopOrderForm($this->entityManager, $this->appId);
        $orderForm->setData($this->dataArray['order']);

        $orderGoodsForm = new ShopOrderGoodsForm();
        $orderGoodsValid= false;
        $orderGoods = [];
        $message    = [];
        if(is_array($this->dataArray['orderGoods']) && !empty($this->dataArray['orderGoods'])) {
            foreach ($this->dataArray['orderGoods'] as $goodsValue) {
                $orderGoodsForm->setData($goodsValue);
                if($orderGoodsForm->isValid()) {
                    $orderGoodsValid = true;
                    $orderGoods[] = $orderGoodsForm->getData();
                }
                else {
                    $orderGoodsValid = false;
                    return $orderGoodsForm->getMessages();
                    break;
                }
            }
        }

        $deliveryForm = new ShopOrderDeliveryAddressForm();
        $deliveryForm->setData($this->dataArray['orderAddress']);
        if($orderForm->isValid() && $orderGoodsValid && $deliveryForm->isValid()) {
            $orderData      = $orderForm->getData();
            $orderAddress   = $deliveryForm->getData();

            $this->entityManager->beginTransaction();
            try {
                $shopOrder = $this->shopOrderManager->addShopOrder($orderData, $this->appId);
                $this->shopOrderGoodsManager->addShopOrderGoods($orderGoods, $shopOrder->getShopOrderId(), $shopOrder, $this->appInfo->getAppGoodsBindType());
                $this->shopOrderDeliveryAddressManager->addShopOrderDeliveryAddress($orderAddress, $shopOrder->getShopOrderId());

                $this->entityManager->commit();
            } catch (\Exception $e) {
                $this->entityManager->rollback();
            }
        } else {
            $message1 = $orderForm->getMessages();
            $message2 = $deliveryForm->getMessages();
            if(!empty($message1)) return $message1;
            if(!empty($message2)) return $message2;
        }

        if(!empty($message)) return $message;

        return $this->dataArray;
    }

    /**
     * 取消订单
     * @return array|bool|\Traversable
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function cancelOrder()
    {
        $orderSnForm = new OrderSnForm();
        $orderSnForm->setData($this->dataArray);
        if($orderSnForm->isValid()) {
            $orderSn    = $orderSnForm->getData('orderSn');
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$orderSn, 'appId'=>$this->appId]);
            if($orderInfo == null || $orderInfo->getShopOrderState() != 10) return false;

            $this->shopOrderManager->changeShopOrderState(0, $orderInfo);
        } else return $orderSnForm->getMessages();

        return $this->dataArray;
    }

    /**
     * 删除订单
     * @return array|bool|\Traversable
     */
    private function deleteOrder()
    {
        $orderSnForm = new OrderSnForm();
        $orderSnForm->setData($this->dataArray);
        if($orderSnForm->isValid()) {
            $orderSn    = $orderSnForm->getData('order_sn');
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$orderSn, 'appId'=>$this->appId]);
            if($orderInfo == null || $orderInfo->getShopOrderState() != 0) return false;

            $shopOrderId = $orderInfo->getShopOrderId();
            $this->entityManager->beginTransaction();
            try {
                $this->shopOrderManager->deleteShopOrder($orderInfo);
                $this->shopOrderGoodsManager->deleteShopOrderGoods($shopOrderId);
                $this->shopOrderDeliveryAddressManager->deleteShopOrderDeliveryAddress($shopOrderId);

                $this->entityManager->commit();
            } catch (\Exception $e) {
                $this->entityManager->rollback();
            }
        } else return $orderSnForm->getMessages();

        return $this->dataArray;
    }

    /**
     * DBShop3.0处理，订单付款
     * @return array|\Traversable
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function dbshop3PaymentOrder()
    {
        $paymentForm = new OrderTimeForm();
        $paymentForm->setData($this->dataArray);
        if($paymentForm->isValid()) {
            $paymentData = $paymentForm->getData();
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$paymentData['order_sn'], 'appId'=>$this->appId]);
            if($orderInfo && $orderInfo->getShopOrderState() >= 10 && $orderInfo->getShopOrderState() < 20) {
                if (isset($this->dataArray['payment_name']) && !empty($this->dataArray['payment_name'])) $orderInfo = $this->shopOrderManager->editShopOrderPayment(['payment_code' => $this->dataArray['payment_code'], 'payment_name' => $this->dataArray['payment_name']], $orderInfo);
                $this->shopOrderManager->paymentFinishShopOrder($paymentData, 20, $orderInfo);
            }
        } else return $paymentForm->getMessages();

        return $this->dataArray;
    }

    /**
     * 付款完成
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function paymentOrder()
    {
        $paymentForm = new OrderTimeForm();
        $paymentForm->setData($this->dataArray);
        if($paymentForm->isValid()) {
            $paymentData = $paymentForm->getData();
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$paymentData['order_sn'], 'appId'=>$this->appId]);
            if($orderInfo && $orderInfo->getShopOrderState() >= 10 && $orderInfo->getShopOrderState() < 20) {
                $this->shopOrderManager->paymentFinishShopOrder($paymentData, 20, $orderInfo);
            }
        } else return $paymentForm->getMessages();

        return $this->dataArray;
    }

    private function dbshop3DeliverOrder()
    {
        $deliveryForm = new OrderDeliveryForm();
        $deliveryForm->setData($this->dataArray);
        if($deliveryForm->isValid()) {
            $deliveryData = $deliveryForm->getData();
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$deliveryData['order_sn'], 'appId'=>$this->appId]);
            if($orderInfo && $orderInfo->getShopOrderState() >= 20 && $orderInfo->getShopOrderState() < 40) {

                $this->entityManager->beginTransaction();
                try {
                    $this->shopOrderManager->deliverFinishShopOrder($deliveryData, 40, $orderInfo);
                    if ($this->dataArray['express_name'] && !empty($this->dataArray['express_name'])) {
                        $this->shopOrderManager->editShopOrderExpressName($this->dataArray['express_name'], $orderInfo);
                    }
                    if(!empty($deliveryData['delivery_number'])) $this->shopOrderDeliveryAddressManager->addShopOrderDeliveryNumber($deliveryData['delivery_number'], $orderInfo->getShopOrderId());

                    $this->getEventManager()->trigger('app-shop.deliver.post', $this, ['orderInfo' => $orderInfo, 'appInfo' => $this->appInfo]);

                    $this->entityManager->commit();
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                }
            }
        } else return $deliveryForm->getMessages();

        return $this->dataArray;
    }

    /**
     * 订单发货
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function deliverOrder()
    {
        $deliveryForm = new OrderDeliveryForm();
        $deliveryForm->setData($this->dataArray);
        if($deliveryForm->isValid()) {
            $deliveryData = $deliveryForm->getData();
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$deliveryData['order_sn'], 'appId'=>$this->appId]);
            if($orderInfo && $orderInfo->getShopOrderState() >= 20 && $orderInfo->getShopOrderState() < 40) {

                $this->entityManager->beginTransaction();
                try {
                    $this->shopOrderManager->deliverFinishShopOrder($deliveryData, 40, $orderInfo);
                    if(!empty($deliveryData['delivery_number'])) $this->shopOrderDeliveryAddressManager->addShopOrderDeliveryNumber($deliveryData['delivery_number'], $orderInfo->getShopOrderId());

                    $this->getEventManager()->trigger('app-shop.deliver.post', $this, ['orderInfo' => $orderInfo, 'appInfo' => $this->appInfo]);

                    $this->entityManager->commit();
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                }
            }
        } else return $deliveryForm->getMessages();

        return $this->dataArray;
    }

    /**
     * 订单确认收货
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function finishOrder()
    {
        $finishForm = new OrderTimeForm();
        $finishForm->setData($this->dataArray);
        if($finishForm->isValid()) {
            $finishData = $finishForm->getData();
            $orderInfo  = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn'=>$finishData['order_sn'], 'appId'=>$this->appId]);
            if($orderInfo && $orderInfo->getShopOrderState() >= 40 && $orderInfo->getShopOrderState() < 60) {
                $this->shopOrderManager->finishShopOrder($finishData, 60, $orderInfo);
                $this->getEventManager()->trigger('app-shop.finish.post', $this, $orderInfo);
            }
        } else return $finishForm->getMessages();

        return $this->dataArray;
    }

    /**
     * 其他商城订单添加
     * @return array|mixed
     */
    private function otherAddOrder()
    {
        return $this->addOrder();
    }

    /**
     * 其他商城订单取消
     * @return array|bool|\Traversable
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function otherCancelOrder()
    {
        return $this->cancelOrder();
    }

    /**
     * 其他商城订单删除
     * @return array|bool|\Traversable
     */
    private function otherDeleteOrder()
    {
        return $this->deleteOrder();
    }

    /**
     * 其他商城订单付款
     * @return array|\Traversable
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function otherPaymentOrder()
    {
        return $this->dbshop3PaymentOrder();
    }

    /**
     * 其他商城订单发货
     * @return array|\Traversable
     */
    public function otherDeliverOrder()
    {
        return $this->dbshop3DeliverOrder();
    }

    /**
     * 其他商城订单收货（完成）
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function otherFinishOrder()
    {
        return $this->finishOrder();
    }
}