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

namespace Admin\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class HelpUrl extends AbstractHelper
{

    const HELP_URL  = 'https://docs.dberp.net/dberp/';
    const URL_STATE = true;

    /**
     * 帮助网址
     * @param string $name
     * @return bool|string
     */
    public function __invoke(string $name)
    {
        if(!self::URL_STATE) return false;

        $urlArray = [
            /*=====================系统========================*/
            'system'    => 'system/',

            'adminList' => 'system/admin.html',
            'adminAdd'  => 'system/admin.html',
            'adminEdit' => 'system/admin.html',
            'adminDel'  => 'system/admin.html',
            'adminPassword' => 'system/admin.html',

            'adminGroupList' => 'system/admin-group.html',
            'adminGroupAdd' => 'system/admin-group.html',
            'adminGroupEdit' => 'system/admin-group.html',

            'regionList'      => 'system/region.html',
            'regionAdd'       => 'system/region.html',
            'regionEdit'      => 'system/region.html',

            'appList'           => 'system/app.html',
            'appAdd'            => 'system/app.html',

            'serviceBind'       => 'system/service-bind.html',
            'erpUpdate'         => 'system/update.html',

            'operLogList' => 'system/log.html',
            /*=====================基础========================*/
            'goodsList' => 'base/',
            'goodsAdd'  => 'base/',
            'importGoods' => 'base/',
            'goodsEdit' => 'base/',
            'goodsPriceTrend' => 'base/',
            'goodsWarehouse' => 'base/',

            'goodsCategoryList' => 'base/class.html',
            'goodsCategoryAdd'  => 'base/class.html',
            'goodsCategoryEdit' => 'base/class.html',

            'warehouseList' => 'base/warehouse.html',
            'warehouseAdd'  => 'base/warehouse.html',
            'warehouseEdit' => 'base/warehouse.html',

            'positionList'  => 'base/warehouse-position.html',
            'positionAdd'   => 'base/warehouse-position.html',
            'positionEdit'  => 'base/warehouse-position.html',

            'unitList'      => 'base/unit.html',
            'unitAdd'       => 'base/unit.html',
            'unitEdit'      => 'base/unit.html',

            'brandList'      => 'base/brand.html',
            'brandAdd'       => 'base/brand.html',
            'brandEdit'      => 'base/brand.html',

            /*=====================库存========================*/
            'otherImport'   => 'stock/',
            'otherEx'       => 'stock/other-ex.html',
            'stockCheck'    => 'stock/check.html',
            'stockTransfer' => 'stock/transfer.html',

            /*=====================销售========================*/
            'salesOrderList' => 'sales/',
            'salesOrderShow' => 'sales/',
            'salesOrderAdd'  => 'sales/',
            'salesOrderEdit' => 'sales/',
            'salesOrderView' => 'sales/',
            'salesSendOrder' => 'sales/',

            'salesSendOrderList' => 'sales/send-order.html',

            'salesOrderReturnList' => 'sales/order-return.html',

            /*=====================采购========================*/
            'pOrderList'    => 'purchase/',
            'pOrderAdd'     => 'purchase/',
            'pOrderEdit'    => 'purchase/',
            'pOrderView'    => 'purchase/',
            'pOrderReturn'  => 'purchase/',

            'orderReturnList'      => 'purchase/purchase-return.html',
            'orderReturnAdd'       => 'purchase/purchase-return.html',

            'warehouseOrderList'    => 'purchase/warehouse-order.html',
            'warehouseOrderAdd'     => 'purchase/warehouse-order.html',
            'warehouseOrderEdit'    => 'purchase/warehouse-order.html',

            /*=====================客户========================*/
            'customerList'  => 'user/',
            'customerAdd'   => 'user/',
            'customerEdit'  => 'user/',

            'customerCategoryList'  => 'user/category.html',
            'customerCategoryAdd'   => 'user/category.html',
            'customerCategoryEdit'  => 'user/category.html',

            'supplierList'  => 'user/supplier.html',
            'supplierAdd'   => 'user/supplier.html',
            'supplierEdit'  => 'user/supplier.html',

            'supplierCategoryList'  => 'user/supplier-category.html',
            'supplierCategoryAdd'   => 'user/supplier-category.html',
            'supplierCategoryEdit'  => 'user/supplier-category.html',

            /*=====================商城========================*/
            'shopOrderList'     => 'shop/',
            'shopOrderGoods'    => 'shop/order-goods.html',

            /*=====================资金========================*/
            'financePayableList' => 'fund/',
            'financePayableView' => 'fund/',
            'financeAddPayable'  => 'fund/',
            'financePayableLog'  => 'fund/',

            'receivablesList'       => 'fund/receivable.html',
            'accountsAddReceivable' => 'fund/receivable.html',
            'accountsReceivableShow'=> 'fund/receivable.html',
            'accountsReceivableLog' => 'fund/receivable.html'
        ];

        if(!isset($urlArray[$name])) return false;

        return '<a href="javascript:;" onclick="openDocsUrl(\''.$this->getView()->translate('DBErp进销存系统教程').'\', \''.self::HELP_URL.$urlArray[$name].'\', \''.$this->getView()->translate('新窗口打开').'\');" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> '.$this->getView()->translate('查看教程').'</a>';
    }
}