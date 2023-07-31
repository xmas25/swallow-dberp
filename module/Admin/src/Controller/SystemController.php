<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright Copyright (c) 2012-2019 DBShop.net Inc. (http://www.dberp.net)
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    北京珑大钜商科技有限公司
 *
 */

namespace Admin\Controller;

use Admin\Entity\System;
use Admin\Form\SystemForm;
use Admin\Service\SystemManager;
use Doctrine\ORM\EntityManager;
use Laminas\Crypt\BlockCipher;
use Laminas\Http\Client;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;

class SystemController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var entityManager
     */
    private $entityManager;

    private $systemManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        SystemManager $systemManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->systemManager    = $systemManager;
    }

    /**
     * 系统设置
     * @return array|\Laminas\Http\Response|\Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction()
    {
        $form = new SystemForm();
        $form->get('website_timezone|base')->setValueOptions($this->adminCommon()->timezoneArray());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                foreach ($data as $dataKey => $dataValue) {
                    $where    = explode('|', $dataKey);
                    $oneSystem= $this->entityManager->getRepository(System::class)->findOneBy(['sysName' => $where[0], 'sysType' => $where[1]]);
                    if ($oneSystem) {
                        if($oneSystem->getSysBody() != $dataValue) $this->systemManager->editSystem($oneSystem, $dataValue);
                    } else {
                        $this->systemManager->addSystem(['sysName' => $where[0], 'sysBody' => $dataValue, 'sysType' => $where[1]]);
                    }
                }

                //写入配置文件
                $this->createConfig()->createSystem();

                $message = $this->translator->translate('系统设置 修改成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('系统设置'));

                return $this->redirect()->toRoute('admin-system');
            }
        }

        $system = $this->entityManager->getRepository(System::class)->findAll();
        $systemArray = [];
        if($system) {
            foreach ($system as $value) {
                $systemArray[$value->getSysName().'|'.$value->getSysType()] = $value->getSysBody();
            }
            $form->setData($systemArray);
        }

        return ['form' => $form];
    }

    private function test()
    {
        $appId = '11555568559909445';
        $appKey = '5caafa4a8f2b24e3361fbb9af38e9675';

        $blockCipher = BlockCipher::factory('openssl');
        $blockCipher->setKey($appKey);

        $postData = array(
            'type' => 'shopping',
            'sn' => '111111',
            'order' => '686868',
            'total' => 200.00,
            'code' => 'xder2323342',
            'receipt' => 'http://xxxx.xxx.xxx/a.jpg'
        );
        $result = $blockCipher->decrypt('ede97f8a434ce9619508516611232376b85bf91bcc53d9ada95b5b405192a065EPSr2tjZ6aWgcXGL8n+bXlgEbUS7/neyF4UNsUiKDf4u7XyWh8g8qdloUlxI6Cf6g/bQGN5focSXxCKTRRs5eBS21WUpi7LlWsRqv4hs82XjOrQ+iJ3YuHPdlo/p6j7xnXMDW+sr9Fo7nIyx4mT8NUwOkiwKRdrrdANKWXyCDKhoQySYq/cZ6g/jgdXTtLRo');
        echo $result;exit;

        //加密，默认值为：AES算法，CBC模式，带SHA256的HMAC，PKCS＃7填充
        $blockCipher = BlockCipher::factory('openssl');
        $blockCipher->setKey('445566');

        $addOrder =             [
            'order'         =>[
                'order_sn'          =>time(),
                'buy_name'          => '斌子',
                'payment_code'      => 'alipay',
                'payment_name'      => '支付宝',
                'payment_cost'      => '',
                'payment_certification'=> '没有凭证',
                'express_code'      => 'sfkd',
                'express_name'      => '顺丰快递',
                'express_cost'      => '',
                'other_cost'        => 10.1,
                'other_info'        => 'dsds',
                'discount_amount'   => 10.1,
                'discount_info'     => '会员优惠',
                'goods_amount'      => '160.12',
                'order_amount'      => '150.12',
                'order_message'     => '没有什么可添加',
                'add_time'          => time()
            ],
            'orderGoods'    =>[
                [
                    'goods_name'    => '刀锋',
                    'goods_spec'    => '锋利',
                    'goods_sn'      => '23312',
                    'unit_name'     => '台',
                    'goods_price'   => '20.00',
                    'goods_type'    => 1,
                    'buy_num'       => 2,
                    'goods_amount'  => '40.00',
                ],
                [
                    'goods_name'    => '连衣裙',
                    'goods_spec'    => '粉色',
                    'goods_sn'      => 'ng00045',
                    'unit_name'     => '台',
                    'goods_price'   => '60.06',
                    'goods_type'    => 1,
                    'buy_num'       => 2,
                    'goods_amount'  => '120.12',
                ]
            ],
            'orderAddress'  =>[
                'delivery_name'         => '斌子',
                'region_info'           => '北京市 朝阳区',
                'region_address'        => '双井优士阁',
                'zip_code'              => '100000',
                'delivery_phone'        => '18625645623',
                'delivery_telephone'    => '',
                'delivery_info'         => '没有备注信息'
            ]
        ];
        $cancelOrder = ['order_sn' => '1554634798', 'oper_time'=> time(), 'delivery_number'=> '66666666sds'];

        $result = $blockCipher->encrypt(Json::encode($cancelOrder));

        $client = new Client('http://192.168.0.7/github/DBERP/public/api', [
            'adapter' => 'Laminas\Http\Client\Adapter\Curl',
            'curloptions' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        $client->setHeaders(['Accept' => 'application/json']);
        $client->setMethod('POST');
        $client->setParameterPost(['appId' => '112233', 'action' => 'deliverOrder', 'dataStr' => $result]);
        $response = $client->send();
        if($response->isSuccess()) {
            echo $response->getContent();
            /*$cArray = Json::decode($response->getContent(), Json::TYPE_ARRAY);
            print_r($cArray);*/
        }
    }
}