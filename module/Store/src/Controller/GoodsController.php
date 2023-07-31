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

namespace Store\Controller;

use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Purchase\Entity\OrderGoods;
use Purchase\Entity\PurchaseGoodsPriceLog;
use Sales\Entity\SalesOrderGoods;
use Store\Entity\Goods;
use Store\Entity\GoodsCategory;
use Store\Entity\GoodsCustom;
use Store\Entity\WarehouseGoods;
use Store\Form\GoodsForm;
use Store\Form\ImportGoodsForm;
use Store\Form\SearchGoodsForm;
use Store\Service\GoodsCategoryManager;
use Store\Service\GoodsCustomManager;
use Store\Service\GoodsManager;
use Laminas\Filter\StaticFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class GoodsController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $goodsCategoryManager;
    private $goodsManager;
    private $goodsCustomManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        GoodsCategoryManager $goodsCategoryManager,
        GoodsManager    $goodsManager,
        GoodsCustomManager $goodsCustomManager
    )
    {
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->goodsCategoryManager = $goodsCategoryManager;
        $this->goodsManager         = $goodsManager;
        $this->goodsCustomManager   = $goodsCustomManager;
    }

    /**
     * 商品列表
     * @return mixed
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchGoodsForm();
        $searchForm->get('goods_category_id')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('商品分类')));
        $searchForm->get('brand_id')->setValueOptions($this->storeCommon()->brandListOptions($this->translator->translate('商品品牌')));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query = $this->entityManager->getRepository(Goods::class)->findAllGoods($search);
        $goodsList = $this->adminCommon()->erpPaginator($query, $page);

        return ['goodsList' => $goodsList, 'searchForm' => $searchForm];
    }

    /**
     * 添加商品
     * @return \Laminas\Http\Response|GoodsForm[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addAction()
    {
        $form = new GoodsForm($this->entityManager);

        $form->get('goodsCategoryId')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('选择商品分类')));
        $form->get('brandId')->setValueOptions($this->storeCommon()->brandListOptions());
        $form->get('unitId')->setValueOptions($this->storeCommon()->unitOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->entityManager->beginTransaction();
                try {
                    $goods = $this->goodsManager->addGoods($data, $this->adminSession('admin_id'));
                    $this->goodsCustomManager->addOrEditGoodsCustom($data, $goods->getGoodsId());
                    $this->getEventManager()->trigger('goods.add.post', $this, $goods);

                    $this->entityManager->commit();
                    $this->adminCommon()->addOperLog(sprintf($this->translator->translate('商品 %s 添加成功！'), $data['goodsName']), $this->translator->translate('商品'));
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('商品添加失败!'));
                }

                return $this->redirect()->toRoute('goods');

            }
        }

        return ['form' => $form];
    }

    /**
     * 批量导入商品
     * @return \Laminas\Http\Response|ImportGoodsForm[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function importGoodsAction()
    {
        @set_time_limit(600);

        $form = new ImportGoodsForm();

        if($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $goodsClassList = $this->entityManager->getRepository(GoodsCategory::class)->findBy([]);
                if (empty($goodsClassList)) {
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('系统中没有商品分类!'));
                    return $this->redirect()->toRoute('goods', ['action' => 'importGoods']);
                }
                $goodsClassIdArray = [];
                foreach ($goodsClassList as $classValue) {
                    $goodsClassIdArray[] = $classValue->getGoodsCategoryId();
                }

                $readerXlsx     = new Xlsx();
                $spreadsheet    = $readerXlsx->load($data['importFile']['tmp_name']);
                $sheetData      = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
                $rowCount       = count($sheetData);
                if ($rowCount == 1) {
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('无导入信息!'));
                    return $this->redirect()->toRoute('goods', ['action' => 'importGoods']);
                }

                $adminId        = $this->adminSession('admin_id');
                $maxGoodsId     = $this->entityManager->getRepository(Goods::class)->getMaxGoodsId();
                $nextGoodsId    = $maxGoodsId == null ? 1 : $maxGoodsId + 1;//已知最大id，那么当前即为将要添加的商品id
                $insertGoodsSql         = '';
                $insertGoodsSqlArray    = [];
                $insertGoodsCustomSql   = '';
                $insertGoodsCustomSqlArray = [];
                for ($num = 2; $num <= $rowCount; $num++) {
                    $goodsName      = addslashes($sheetData[$num]['A']);
                    $goodsNumber    = $sheetData[$num]['B'];
                    $goodsCategoryId= intval($sheetData[$num]['C']);
                    $unitId         = intval($sheetData[$num]['D']);
                    $goodsRecommendPrice = floatval($sheetData[$num]['E']);
                    $goodsBarcode   = $sheetData[$num]['F'];
                    $brandId        = intval($sheetData[$num]['G']);
                    $goodsSpec      = addslashes($sheetData[$num]['H']);
                    $goodsInfo      = addslashes($sheetData[$num]['I']);
                    $goodsSort      = 255;
                    $extendBody     = $sheetData[$num]['J'];

                    if (empty($goodsName)) continue;

                    $insertGoodsSql .= "($nextGoodsId, '".$goodsName."', '".$goodsNumber."', ".$goodsCategoryId.", ".$unitId.", '".$goodsRecommendPrice."', '".$goodsBarcode."', ".$brandId.", '".$goodsSpec."', '".$goodsInfo."', ".$goodsSort.", ".$adminId."),";

                    if (!empty($extendBody)) {
                        $extendBody         = str_replace('：', ':', $extendBody);
                        $extendBodyArray    = explode('|', $extendBody);
                        if (!empty($extendBodyArray)) foreach ($extendBodyArray as $bodyKey => $bodyValue) {
                            if (($bodyKey+1) > 10) continue;
                            $bodyValueArray = explode(':', $bodyValue);
                            if (!empty($bodyValueArray[0]) && !empty($bodyValueArray[1])) {
                                $insertGoodsCustomSql .= "($nextGoodsId, '".addslashes($bodyValueArray[0])."', '".addslashes($bodyValueArray[1])."', ".($bodyKey+1)."),";
                            }
                        }
                    }

                    if ($num%3000 == 0) {
                        $insertGoodsSqlArray[] = rtrim($insertGoodsSql, ',');
                        $insertGoodsSql = '';

                        if (!empty($insertGoodsCustomSql)) {
                            $insertGoodsCustomSqlArray[] = rtrim($insertGoodsCustomSql, ',');
                            $insertGoodsCustomSql = '';
                        }
                    }

                    $nextGoodsId++;
                }

                if (!empty($insertGoodsSql)) {
                    $this->entityManager->beginTransaction();
                    try {
                        //商品导入
                        $insertGoodsSql = "INSERT INTO `dberp_goods` (`goods_id`, `goods_name`, `goods_number`, `goods_category_id`, `unit_id`, `goods_recommend_price`, `goods_barcode`, `brand_id`, `goods_spec`, `goods_info`, `goods_sort`, `admin_id`) VALUES ".rtrim($insertGoodsSql, ',');
                        $stmt = $this->entityManager->getConnection()->prepare($insertGoodsSql);
                        $stmt->execute();
                        if (!empty($insertGoodsSqlArray)) foreach ($insertGoodsSqlArray as $insertGoodsSqlValue) {
                            $insertGoodsSql = "INSERT INTO `dberp_goods` (`goods_id`, `goods_name`, `goods_number`, `goods_category_id`, `unit_id`, `goods_recommend_price`, `goods_barcode`, `brand_id`, `goods_spec`, `goods_info`, `goods_sort`, `admin_id`) VALUES ".$insertGoodsSqlValue;
                            $stmt = $this->entityManager->getConnection()->prepare($insertGoodsSql);
                            $stmt->execute();
                        }

                        //商品扩展信息导入
                        if (!empty($insertGoodsCustomSql)) {
                            $insertGoodsCustomSql = "INSERT INTO `dberp_goods_custom` (`goods_id`, `custom_title`, `custom_content`, `custom_key`) VALUES ".rtrim($insertGoodsCustomSql, ',');
                            $stmt = $this->entityManager->getConnection()->prepare($insertGoodsCustomSql);
                            $stmt->execute();
                            if (!empty($insertGoodsCustomSqlArray)) foreach ($insertGoodsCustomSqlArray as $insertGoodsCustomSqlValue) {
                                $insertGoodsCustomSql = "INSERT INTO `dberp_goods_custom` (`goods_id`, `custom_title`, `custom_content`, `custom_key`) VALUES ".$insertGoodsCustomSqlValue;
                                $stmt = $this->entityManager->getConnection()->prepare($insertGoodsCustomSql);
                                $stmt->execute();
                            }
                        }

                        $this->entityManager->commit();
                        $this->adminCommon()->addOperLog($this->translator->translate('商品批量导入成功!'), $this->translator->translate('商品'));
                    } catch (\Exception $e) {
                        $this->entityManager->rollback();
                        $this->flashMessenger()->addWarningMessage($this->translator->translate('商品批量导入不成功!'));
                    }
                }
                return $this->redirect()->toRoute('goods');
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * 编辑商品
     * @return array|\Laminas\Http\Response
     */
    public function editAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        $form = new GoodsForm($this->entityManager, $goodsInfo);

        $form->get('goodsCategoryId')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('选择商品分类')));
        $form->get('brandId')->setValueOptions($this->storeCommon()->brandListOptions());
        $form->get('unitId')->setValueOptions($this->storeCommon()->unitOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->entityManager->beginTransaction();
                try {
                    $this->goodsManager->editGoods($data, $goodsInfo);
                    $this->goodsCustomManager->addOrEditGoodsCustom($data, $goodsId);
                    $this->getEventManager()->trigger('goods.edit.post', $this, $goodsInfo);

                    $this->entityManager->commit();
                    $this->adminCommon()->addOperLog(sprintf($this->translator->translate('商品 %s 编辑成功！'), $data['goodsName']), $this->translator->translate('商品'));
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('商品编辑失败!'));
                }

                return $this->redirect()->toRoute('goods');
            }
        } else $form->setData($goodsInfo->valuesArray());

        //商品自定义
        $inGoodsCustom  = [];
        $goodsCustom    = $this->entityManager->getRepository(GoodsCustom::class)->findBy(['goodsId' => $goodsId]);
        if($goodsCustom) foreach ($goodsCustom as $cValue) {
            $inGoodsCustom['customTitle'.$cValue->getCustomKey()] = $cValue->getCustomTitle();
            $inGoodsCustom['customContent'.$cValue->getCustomKey()] = $cValue->getCustomContent();
        }
        if (!empty($inGoodsCustom)) $form->setData($inGoodsCustom);

        return ['goods' => $goodsInfo, 'form' => $form];
    }

    /**
     * 商品名称检索，ajax输出
     * @return JsonModel
     */
    public function autoCompleteGoodsSearchAction(): JsonModel
    {
        $array = [];

        $query = StaticFilter::execute($this->params()->fromQuery('query', ''), 'StripTags');
        $query = StaticFilter::execute($query, 'HtmlEntities');

        $goodsSearch = $this->entityManager->getRepository(Goods::class)->findGoodsNameSearch($query);
        if($goodsSearch) {
            foreach ($goodsSearch as $item) {
                $array[] = ['id'=>$item['goodsId'], 'label'=>$item['goodsName'] . (!empty($item['goodsSpec']) ? ' - '.$item['goodsSpec'] : '')];
            }
        }
        return new JsonModel($array);
    }

    /**
     * 商品id检索，ajax输出
     * @return JsonModel
     */
    public function goodsIdSearchAction(): JsonModel
    {
        $array = ['state' => 'false'];

        $goodsId = (int) $this->params()->fromPost('goodsId', 0);
        $warehouseId = (int) $this->params()->fromPost('warehouseId', 0);

        if($goodsId > 0) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsId]);
            if($goodsInfo) {
                $array['state'] = 'ok';
                $array['result'] = $goodsInfo->goodsValuesArray();

                $warehouseGoodsNum  = 0;
                if($warehouseId > 0) {
                    $goodsWarehouseInfo = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseId, 'goodsId' => $goodsId]);
                    if($goodsWarehouseInfo) {
                        $warehouseGoodsNum = $goodsWarehouseInfo->getWarehouseGoodsStock();
                    }
                }
                $array['result']['warehouseGoodsNum'] = $warehouseGoodsNum;
            }
        }

        return new JsonModel($array);
    }

    /**
     * 删除商品
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $oneOrderGoods = $this->entityManager->getRepository(OrderGoods::class)->findOneByGoodsId($goodsId);
        $oneSalesGoods = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneByGoodsId($goodsId);
        if($oneOrderGoods || $oneSalesGoods) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('订单中存在该商品，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->goodsCustomManager->deleteGoodsCustomGoodsId($goodsInfo->getGoodsId());
        $this->goodsManager->deleteGoods($goodsInfo);
        $this->getEventManager()->trigger('goods.del.post', $this, ['goodsId' => $goodsId]);

        $message = sprintf($this->translator->translate('商品 %s 删除成功！'), $goodsInfo->getGoodsName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('商品'));

        return $this->adminCommon()->toReferer();
    }

    /**
     * 采购价格趋势
     * @return array|\Laminas\Http\Response
     */
    public function priceTrendAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        $priceTrend = $this->entityManager->getRepository(PurchaseGoodsPriceLog::class)->findBy(['goodsId' => $goodsId], ['priceLogId' => 'DESC']);
        $priceArray = [];
        if(!empty($priceTrend)) {
            foreach ($priceTrend as $priceValue) {
                $priceArray['price'][]  = number_format($priceValue->getGoodsPrice(), 2, '.', '');
                $priceArray['date'][]   = "'" . date("Y-m-d H:i", $priceValue->getLogTime()) ."'";
            }
        }

        return ['goodsInfo' => $goodsInfo, 'priceArray' => $priceArray];
    }

    /**
     * 单个商品在仓库中的分布
     * @return array|\Laminas\Http\Response
     */
    public function goodsWarehouseAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        //$warehouseGoodsList = $this->entityManager->getRepository(WarehouseGoods::class)->findBy(['goodsId' => $goodsId]);
        $warehouseGoodsList = $this->entityManager->getRepository(WarehouseGoods::class)->findWarehouseGoods($goodsId);
        $warehouseArray = [];
        if(!empty($warehouseGoodsList)) {
            foreach ($warehouseGoodsList as $value) {
                $warehouseArray['title'][] = "'" . $value->getOneWarehouse()->getWarehouseName() . "'";
                $warehouseArray['value'][] = "{value:".$value->getWarehouseGoodsStock().", name:'".$value->getOneWarehouse()->getWarehouseName()."'}";
            }
        }

        return ['goodsInfo' => $goodsInfo, 'warehouseGoods'=>$warehouseGoodsList, 'warehouseArray' => $warehouseArray];
    }

    /**
     * ajax获取商品列表
     * @return ViewModel
     */
    public function ajaxGoodsSearchAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);

        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchGoodsName = trim($this->params()->fromQuery('searchGoodsName'));
        if(!empty($searchGoodsName)) {
            $searchGoodsName = StaticFilter::execute($searchGoodsName, 'StripTags');
            $searchGoodsName = StaticFilter::execute($searchGoodsName, 'HtmlEntities');
            $search['goods_name'] = $searchGoodsName;
        }
        $query = $this->entityManager->getRepository(Goods::class)->findAllGoods($search);
        $goodsList = $this->adminCommon()->erpPaginator($query, $page);

        return $view->setVariables(['goodsList' => $goodsList, 'searchGoodsName' => $searchGoodsName]);
    }
}