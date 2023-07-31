DROP TABLE IF EXISTS `dberp_accounts_receivable`;
CREATE TABLE IF NOT EXISTS `dberp_accounts_receivable` (
  `receivable_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(11) NOT NULL,
  `sales_order_sn` varchar(50) NOT NULL,
  `send_order_id` int(11) NOT NULL,
  `send_order_sn` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `receivable_code` varchar(20) NOT NULL,
  `receivable_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `finish_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `add_time` int(10) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`receivable_id`),
  KEY `receivable_index` (`sales_order_id`,`send_order_id`,`customer_id`,`add_time`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应收账款表';

DROP TABLE IF EXISTS `dberp_accounts_receivable_log`;
CREATE TABLE IF NOT EXISTS `dberp_accounts_receivable_log` (
  `receivable_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `receivable_id` int(11) NOT NULL,
  `receivable_log_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `receivable_log_user` varchar(100) NOT NULL,
  `receivable_log_time` int(10) NOT NULL,
  `receivable_file` varchar(255) DEFAULT NULL,
  `receivable_info` varchar(255) DEFAULT NULL,
  `receivable_add_time` int(10) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`receivable_log_id`),
  KEY `receivable_log_index` (`receivable_id`,`receivable_add_time`,`receivable_log_time`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收款记录';

DROP TABLE IF EXISTS `dberp_admin`;
CREATE TABLE IF NOT EXISTS `dberp_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_group_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_passwd` varchar(72) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_state` tinyint(2) NOT NULL DEFAULT '1',
  `admin_add_time` int(10) NOT NULL,
  `admin_old_login_time` int(10) DEFAULT NULL,
  `admin_new_login_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `admin_name` (`admin_name`,`admin_email`),
  KEY `admin_group_id` (`admin_group_id`),
  KEY `admin_state` (`admin_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_admin_group`;
CREATE TABLE IF NOT EXISTS `dberp_admin_group` (
  `admin_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_group_name` varchar(200) NOT NULL,
  `admin_group_purview` text,
  PRIMARY KEY (`admin_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_app`;
CREATE TABLE IF NOT EXISTS `dberp_app` (
    `app_id` int(11) NOT NULL AUTO_INCREMENT,
    `app_name` varchar(100) NOT NULL,
    `app_access_id` varchar(30) NOT NULL,
    `app_access_secret` varchar(50) NOT NULL,
    `app_url` varchar(100) NOT NULL,
    `app_url_port` varchar(10) NOT NULL DEFAULT '80',
    `app_type` varchar(20) NOT NULL,
    `app_goods_bind_type` varchar(20) DEFAULT NULL COMMENT '商品绑定类型',
    `app_goods_bind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用商品绑定',
    `app_goods_warehouse` varchar(300) DEFAULT NULL,
    `app_state` tinyint(2) NOT NULL DEFAULT '1',
    `app_add_time` int(10) NOT NULL,
    PRIMARY KEY (`app_id`),
    KEY `dberp_app_index` (`app_access_id`,`app_access_secret`,`app_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='绑定电商系统表';

DROP TABLE IF EXISTS `dberp_brand`;
CREATE TABLE IF NOT EXISTS `dberp_brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `brand_code` varchar(30) DEFAULT NULL,
  `brand_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `dberp_brand_index` (`brand_name`,`brand_code`,`brand_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_customer`;
CREATE TABLE IF NOT EXISTS `dberp_customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_category_id` int(11) NOT NULL,
  `customer_code` varchar(30) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_sort` int(11) NOT NULL DEFAULT '255',
  `customer_email` varchar(30) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_contacts` varchar(30) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_telephone` varchar(20) DEFAULT NULL,
  `customer_bank` varchar(100) DEFAULT NULL,
  `customer_bank_account` varchar(30) DEFAULT NULL,
  `customer_tax` varchar(30) DEFAULT NULL,
  `customer_info` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL DEFAULT '0',
  `region_values` varchar(100) NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `dberp_customer_index` (`customer_code`,`customer_sort`,`customer_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户表';

DROP TABLE IF EXISTS `dberp_customer_category`;
CREATE TABLE IF NOT EXISTS `dberp_customer_category` (
  `customer_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_category_code` varchar(30) NOT NULL,
  `customer_category_name` varchar(100) NOT NULL,
  `customer_category_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`customer_category_id`),
  KEY `dberp_customer_category_index` (`customer_category_code`,`customer_category_name`,`customer_category_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户分类';

DROP TABLE IF EXISTS `dberp_finance_payable`;
CREATE TABLE IF NOT EXISTS `dberp_finance_payable` (
  `payable_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_order_id` int(11) NOT NULL COMMENT '入库单号',
  `p_order_id` int(11) NOT NULL COMMENT '采购订单id',
  `p_order_sn` varchar(50) NOT NULL COMMENT '采购订单号',
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `payment_code` varchar(20) NOT NULL COMMENT '支付方式code',
  `payment_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '采购支付金额',
  `finish_amount` decimal(19,4) DEFAULT '0.0000' COMMENT '采购已经支付金额',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`payable_id`),
  KEY `dberp_finance_payment_index` (`warehouse_order_id`,`p_order_id`,`supplier_id`,`payment_code`,`admin_id`,`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_finance_payable_log`;
CREATE TABLE IF NOT EXISTS `dberp_finance_payable_log` (
  `pay_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `payable_id` int(11) NOT NULL COMMENT '应付款账单id',
  `pay_log_amount` decimal(19,4) DEFAULT '0.0000' COMMENT '付款金额',
  `pay_log_user` varchar(100) NOT NULL COMMENT '付款人姓名',
  `pay_log_paytime` int(10) NOT NULL COMMENT '付款时间',
  `pay_file` varchar(255) DEFAULT NULL,
  `pay_log_info` varchar(255) DEFAULT NULL COMMENT '付款备注信息',
  `pay_log_addtime` int(10) NOT NULL COMMENT '记录添加时间',
  `admin_id` int(11) NOT NULL COMMENT '操作者id',
  PRIMARY KEY (`pay_log_id`),
  KEY `dberp_finance_payable_log_index` (`pay_log_user`,`pay_log_paytime`,`pay_log_addtime`,`payable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_goods`;
CREATE TABLE IF NOT EXISTS `dberp_goods` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_category_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT '0',
  `goods_name` varchar(100) NOT NULL,
  `goods_stock` int(11) DEFAULT '0',
  `goods_spec` varchar(100) DEFAULT NULL,
  `goods_number` varchar(30) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `goods_barcode` varchar(30) DEFAULT NULL,
  `goods_info` varchar(500) DEFAULT NULL,
  `goods_sort` int(11) NOT NULL DEFAULT '255',
  `goods_price` decimal(19,4) DEFAULT '0.0000',
  `admin_id` int(11) NOT NULL,
  `goods_recommend_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`goods_id`),
  KEY `dberp_goods_index` (`goods_name`,`brand_id`,`goods_spec`,`goods_category_id`,`goods_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='基础商品表';

DROP TABLE IF EXISTS `dberp_goods_custom`;
CREATE TABLE IF NOT EXISTS `dberp_goods_custom` (
  `custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `custom_title` varchar(50) NOT NULL,
  `custom_content` varchar(200) NOT NULL,
  `custom_key` int(2) NOT NULL,
  PRIMARY KEY (`custom_id`),
  KEY `goods_id` (`goods_id`),
  KEY `custom_key` (`custom_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品自定义表';

DROP TABLE IF EXISTS `dberp_goods_category`;
CREATE TABLE IF NOT EXISTS `dberp_goods_category` (
  `goods_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_category_top_id` int(11) NOT NULL DEFAULT '0',
  `goods_category_code` varchar(30) NOT NULL,
  `goods_category_name` varchar(100) NOT NULL,
  `goods_category_path` varchar(255) DEFAULT '0',
  `goods_category_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`goods_category_id`),
  KEY `dberp_goods_category_index` (`goods_category_code`,`goods_category_name`,`goods_category_sort`,`goods_category_top_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_operlog`;
CREATE TABLE IF NOT EXISTS `dberp_operlog` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_oper_user` varchar(100) NOT NULL,
  `log_oper_user_group` varchar(100) NOT NULL,
  `log_time` int(10) NOT NULL,
  `log_ip` varchar(50) NOT NULL,
  `log_body` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `dberp_oper_log_index` (`log_oper_user`,`log_oper_user_group`,`log_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作记录';

DROP TABLE IF EXISTS `dberp_other_warehouse_order`;
CREATE TABLE IF NOT EXISTS `dberp_other_warehouse_order` (
  `other_warehouse_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `warehouse_order_sn` varchar(50) NOT NULL,
  `warehouse_order_state` tinyint(1) NOT NULL DEFAULT '3',
  `warehouse_order_info` varchar(255) DEFAULT NULL,
  `warehouse_order_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_order_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_order_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `other_add_time` int(10) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`other_warehouse_order_id`),
  KEY `other_warehouse_order_index` (`warehouse_id`,`warehouse_order_sn`,`warehouse_order_state`,`admin_id`),
  KEY `other_add_time` (`other_add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='其他入库表';

DROP TABLE IF EXISTS `dberp_other_warehouse_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_other_warehouse_order_goods` (
  `warehouse_order_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `other_warehouse_order_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `warehouse_goods_buy_num` int(11) NOT NULL,
  `warehouse_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_number` varchar(30) NOT NULL,
  `goods_spec` varchar(100) DEFAULT NULL,
  `goods_unit` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`warehouse_order_goods_id`),
  KEY `other_warehouse_order_goods_index` (`other_warehouse_order_id`,`warehouse_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='其他入库商品表';

DROP TABLE IF EXISTS `dberp_position`;
CREATE TABLE IF NOT EXISTS `dberp_position` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_sn` varchar(30) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`position_id`),
  KEY `position_sn` (`position_sn`,`warehouse_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仓库仓位';

DROP TABLE IF EXISTS `dberp_purchase_goods_price_log`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_goods_price_log` (
  `price_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `goods_price` decimal(19,0) NOT NULL,
  `p_order_id` int(11) NOT NULL,
  `log_time` int(10) NOT NULL,
  PRIMARY KEY (`price_log_id`),
  KEY `purchase_price_log_index` (`goods_id`,`goods_price`,`p_order_id`,`log_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采购商品价格历史记录';

DROP TABLE IF EXISTS `dberp_purchase_oper_log`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_oper_log` (
  `oper_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_order_id` int(11) NOT NULL,
  `order_state` tinyint(2) NOT NULL,
  `oper_user_id` int(11) NOT NULL,
  `oper_user` varchar(100) NOT NULL,
  `oper_time` int(10) NOT NULL,
  PRIMARY KEY (`oper_log_id`),
  KEY `p_oper_log` (`p_order_id`,`order_state`,`oper_time`),
  KEY `oper_user_id` (`oper_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采购操作记录';

DROP TABLE IF EXISTS `dberp_purchase_order`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_order` (
  `p_order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '采购单id',
  `p_order_sn` varchar(50) NOT NULL COMMENT '采购单编号',
  `supplier_id` int(11) NOT NULL COMMENT '供应商id',
  `supplier_contacts` varchar(30) NOT NULL COMMENT '供应商联系人',
  `supplier_phone` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `supplier_telephone` varchar(20) DEFAULT NULL COMMENT '座机号码',
  `p_order_goods_amount` decimal(19,4) DEFAULT '0.0000' COMMENT '商品总额',
  `p_order_tax_amount` decimal(19,4) DEFAULT '0.0000' COMMENT '税金总额',
  `p_order_amount` decimal(19,4) DEFAULT '0.0000' COMMENT '订单总额',
  `p_order_info` varchar(500) DEFAULT NULL COMMENT '备注信息',
  `p_order_state` tinyint(4) DEFAULT '0' COMMENT '采购单状态，0 未审核，1 已审核，2 已入库，-1 退货，-2 退货完成',
  `payment_code` varchar(20) NOT NULL,
  `return_state` tinyint(2) DEFAULT '0',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`p_order_id`),
  KEY `dberp_purchase_order_index` (`p_order_sn`,`p_order_state`,`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_purchase_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_order_goods` (
  `p_goods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '采购单商品id',
  `p_order_id` int(11) NOT NULL COMMENT '采购单id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_number` varchar(30) NOT NULL COMMENT '商品编号',
  `goods_spec` varchar(100) DEFAULT NULL COMMENT '商品规格',
  `goods_unit` varchar(20) DEFAULT NULL COMMENT '商品单位，非对应id，单位名称',
  `p_goods_buy_num` int(11) NOT NULL COMMENT '商品购买数量',
  `p_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '商品购买的单价',
  `p_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '商品税金',
  `p_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '商品总金额',
  `p_goods_info` varchar(255) DEFAULT NULL COMMENT '商品备注',
  PRIMARY KEY (`p_goods_id`),
  KEY `dberp_purchase_order_goods_index` (`goods_number`,`goods_name`,`goods_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_purchase_order_goods_return`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_order_goods_return` (
  `goods_return_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退货商品id',
  `order_return_id` int(11) NOT NULL COMMENT '退货单id',
  `p_goods_id` int(11) NOT NULL COMMENT '采购商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_number` varchar(50) NOT NULL COMMENT '商品编号',
  `goods_spec` varchar(100) DEFAULT NULL COMMENT '商品规格',
  `goods_unit` varchar(20) NOT NULL COMMENT '商品单位',
  `p_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '单品采购价',
  `p_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '税费',
  `goods_return_num` int(11) NOT NULL DEFAULT '0' COMMENT '退货数量',
  `goods_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '退货金额',
  PRIMARY KEY (`goods_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退货商品';

DROP TABLE IF EXISTS `dberp_purchase_order_return`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_order_return` (
  `order_return_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退货单id',
  `p_order_id` int(11) NOT NULL COMMENT '采购订单id',
  `p_order_sn` varchar(50) NOT NULL COMMENT '采购单编号',
  `p_order_goods_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `p_order_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '退货单金额',
  `p_order_return_info` varchar(500) DEFAULT NULL COMMENT '退货原因',
  `return_time` int(10) NOT NULL COMMENT '退货单添加时间',
  `return_state` tinyint(2) NOT NULL DEFAULT '-1',
  `return_finish_time` int(10) DEFAULT NULL,
  `admin_id` int(11) NOT NULL COMMENT '操作者id',
  PRIMARY KEY (`order_return_id`),
  KEY `dberp_purchase_order_return_index` (`p_order_id`,`p_order_sn`,`return_time`,`p_order_return_amount`),
  KEY `return_state` (`return_state`,`return_finish_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退货单';

DROP TABLE IF EXISTS `dberp_purchase_warehouse_order`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_warehouse_order` (
  `warehouse_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_order_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `warehouse_order_sn` varchar(50) NOT NULL,
  `warehouse_order_state` tinyint(1) DEFAULT '2',
  `warehouse_order_info` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `warehouse_order_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_order_tax` decimal(19,4) DEFAULT '0.0000',
  `warehouse_order_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_order_payment_code` varchar(20) NOT NULL,
  PRIMARY KEY (`warehouse_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_purchase_warehouse_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_purchase_warehouse_order_goods` (
  `warehouse_order_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_order_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `p_order_id` int(11) NOT NULL,
  `warehouse_goods_buy_num` int(11) DEFAULT '0',
  `warehouse_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `warehouse_goods_tax` decimal(19,4) DEFAULT '0.0000',
  `warehouse_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_number` varchar(30) NOT NULL,
  `goods_spec` varchar(100) DEFAULT NULL,
  `goods_unit` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`warehouse_order_goods_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_region`;
CREATE TABLE IF NOT EXISTS `dberp_region` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `region_name` varchar(50) NOT NULL,
  `region_top_id` int(11) NOT NULL DEFAULT '0',
  `region_sort` int(11) NOT NULL DEFAULT '255',
  `region_path` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`region_id`),
  KEY `dberp_region_index` (`region_name`,`region_sort`,`region_top_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区数据表';

DROP TABLE IF EXISTS `dberp_sales_goods_price_log`;
CREATE TABLE IF NOT EXISTS `dberp_sales_goods_price_log` (
  `price_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `goods_price` decimal(19,0) NOT NULL,
  `sales_order_id` int(11) NOT NULL,
  `log_time` int(10) NOT NULL,
  PRIMARY KEY (`price_log_id`),
  KEY `sales_price_log_index` (`goods_id`,`sales_order_id`,`log_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售商品价格历史记录';

DROP TABLE IF EXISTS `dberp_sales_oper_log`;
CREATE TABLE IF NOT EXISTS `dberp_sales_oper_log` (
  `oper_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(11) NOT NULL,
  `order_state` tinyint(2) NOT NULL,
  `oper_user_id` int(11) NOT NULL,
  `oper_user` varchar(100) NOT NULL,
  `oper_time` int(10) NOT NULL,
  PRIMARY KEY (`oper_log_id`),
  KEY `s_oper_log_index` (`sales_order_id`,`order_state`,`oper_user_id`,`oper_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售操作记录';

DROP TABLE IF EXISTS `dberp_sales_order`;
CREATE TABLE IF NOT EXISTS `dberp_sales_order` (
  `sales_order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售订单id',
  `sales_order_sn` varchar(50) NOT NULL COMMENT '销售订单编号',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `customer_contacts` varchar(30) NOT NULL COMMENT '客户联系人',
  `customer_address` varchar(255) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL COMMENT '客户手机',
  `customer_telephone` varchar(20) DEFAULT NULL COMMENT '客户电话',
  `sales_order_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '客户购买商品金额',
  `sales_order_tax_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '商品税金',
  `sales_order_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '客户购买商品总额',
  `receivables_code` varchar(20) NOT NULL COMMENT '支付方式',
  `sales_order_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '销售状态',
  `sales_order_info` varchar(500) DEFAULT NULL COMMENT '备注说明',
  `return_state` tinyint(2) NOT NULL DEFAULT '0' COMMENT '退货状态',
  `admin_id` int(11) NOT NULL COMMENT '操作者id',
  PRIMARY KEY (`sales_order_id`),
  KEY `dberp_sales_order_index` (`sales_order_sn`,`customer_id`,`receivables_code`,`sales_order_state`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售订单';

DROP TABLE IF EXISTS `dberp_sales_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_sales_order_goods` (
  `sales_goods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售商品id',
  `sales_order_id` int(11) NOT NULL COMMENT '销售订单id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_number` varchar(30) NOT NULL COMMENT '商品编号',
  `goods_spec` varchar(100) DEFAULT NULL COMMENT '商品规格',
  `goods_unit` varchar(20) NOT NULL COMMENT '商品单位',
  `sales_goods_sell_num` int(11) NOT NULL COMMENT '销售商品数量',
  `sales_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_goods_info` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sales_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售商品表';

DROP TABLE IF EXISTS `dberp_sales_order_goods_return`;
CREATE TABLE IF NOT EXISTS `dberp_sales_order_goods_return` (
  `goods_return_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_order_return_id` int(11) NOT NULL,
  `sales_goods_id` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_number` varchar(50) NOT NULL,
  `goods_spec` varchar(100) DEFAULT NULL,
  `goods_unit` varchar(20) NOT NULL,
  `sales_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `goods_return_num` int(11) NOT NULL DEFAULT '0',
  `goods_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`goods_return_id`),
  KEY `sales_order_goods_return_index` (`sales_order_return_id`,`sales_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售退货商品表';

DROP TABLE IF EXISTS `dberp_sales_order_return`;
CREATE TABLE IF NOT EXISTS `dberp_sales_order_return` (
  `sales_order_return_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(11) NOT NULL,
  `sales_order_sn` varchar(50) NOT NULL,
  `sales_send_order_id` int(11) NOT NULL,
  `sales_send_order_sn` varchar(50) NOT NULL,
  `sales_order_goods_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_order_return_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `sales_order_return_info` varchar(500) DEFAULT NULL,
  `return_time` int(10) NOT NULL,
  `return_state` tinyint(2) NOT NULL,
  `return_finish_time` int(10) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`sales_order_return_id`),
  KEY `sales_order_return_index` (`sales_order_id`,`sales_order_sn`,`sales_send_order_id`,`sales_send_order_sn`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='销售退货单表';

DROP TABLE IF EXISTS `dberp_sales_send_order`;
CREATE TABLE IF NOT EXISTS `dberp_sales_send_order` (
  `send_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `send_order_sn` varchar(50) NOT NULL,
  `sales_order_id` int(11) NOT NULL,
  `return_state` tinyint(1) NOT NULL DEFAULT '0',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`send_order_id`),
  KEY `send_order_index` (`sales_order_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单发货表';

DROP TABLE IF EXISTS `dberp_sales_send_warehouse_goods`;
CREATE TABLE IF NOT EXISTS `dberp_sales_send_warehouse_goods` (
  `send_warehouse_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `send_goods_stock` int(11) NOT NULL DEFAULT '0',
  `send_order_id` int(11) NOT NULL,
  `sales_order_id` int(11) NOT NULL,
  PRIMARY KEY (`send_warehouse_goods_id`),
  KEY `send_warehouse_goods_index` (`goods_id`,`warehouse_id`,`send_goods_stock`,`send_order_id`,`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单发货仓库商品出库表';

DROP TABLE IF EXISTS `dberp_shop_order`;
CREATE TABLE IF NOT EXISTS `dberp_shop_order` (
    `shop_order_id` int(11) NOT NULL AUTO_INCREMENT,
    `shop_order_sn` varchar(50) CHARACTER SET utf8 NOT NULL,
    `shop_buy_name` varchar(100) CHARACTER SET utf8 NOT NULL,
    `shop_payment_code` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
    `shop_payment_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
    `shop_payment_cost` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `shop_payment_certification` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
    `shop_express_code` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
    `shop_express_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
    `shop_express_cost` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `shop_order_other_cost` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `shop_order_other_info` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
    `shop_order_state` tinyint(2) NOT NULL DEFAULT '10',
    `shop_order_discount_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `shop_order_discount_info` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
    `shop_order_goods_amount` decimal(19,4) NOT NULL,
    `shop_order_amount` decimal(19,4) NOT NULL,
    `shop_order_add_time` int(10) NOT NULL,
    `shop_order_pay_time` int(10) DEFAULT NULL,
    `shop_order_express_time` int(10) DEFAULT NULL,
    `shop_order_finish_time` int(10) DEFAULT NULL,
    `shop_order_message` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
    `app_id` int(11) NOT NULL,
    PRIMARY KEY (`shop_order_id`),
    KEY `dberp_shop_order_index` (`shop_payment_code`,`shop_express_code`,`shop_order_state`,`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城订单';

DROP TABLE IF EXISTS `dberp_shop_order_delivery_address`;
CREATE TABLE IF NOT EXISTS `dberp_shop_order_delivery_address` (
  `delivery_address_id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_name` varchar(100) NOT NULL,
  `region_info` varchar(50) DEFAULT NULL,
  `region_address` varchar(300) NOT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `delivery_phone` varchar(20) NOT NULL,
  `delivery_telephone` varchar(20) DEFAULT NULL,
  `delivery_number` varchar(30) DEFAULT NULL,
  `delivery_info` varchar(500) DEFAULT NULL,
  `shop_order_id` int(11) NOT NULL,
  PRIMARY KEY (`delivery_address_id`),
  KEY `shop_order_id` (`shop_order_id`),
  KEY `dilivery_number` (`delivery_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城订单配送地址';

DROP TABLE IF EXISTS `dberp_shop_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_shop_order_goods` (
    `order_goods_id` int(11) NOT NULL AUTO_INCREMENT,
    `shop_order_id` int(11) NOT NULL,
    `distribution_state` int(1) NOT NULL DEFAULT '3',
    `warehouse_name` varchar(100) DEFAULT NULL,
    `warehouse_id` int(11) DEFAULT '0',
    `goods_name` varchar(100) CHARACTER SET utf8 NOT NULL,
    `goods_spec` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
    `goods_sn` varchar(30) CHARACTER SET utf8 NOT NULL,
    `goods_barcode` varchar(30) DEFAULT NULL,
    `goods_unit_name` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
    `goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `goods_type` tinyint(1) NOT NULL DEFAULT '1',
    `buy_num` int(11) NOT NULL,
    `goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
    PRIMARY KEY (`order_goods_id`),
    KEY `shop_order_goods_index` (`shop_order_id`,`goods_type`,`buy_num`,`goods_amount`),
    KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城订单商品';

DROP TABLE IF EXISTS `dberp_stock_check`;
CREATE TABLE IF NOT EXISTS `dberp_stock_check` (
  `stock_check_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `stock_check_sn` varchar(50) NOT NULL COMMENT '盘点单号',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库id',
  `stock_check_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '盘点金额',
  `stock_check_user` varchar(100) NOT NULL COMMENT '盘点人',
  `stock_check_info` varchar(255) NOT NULL COMMENT '盘点备注',
  `stock_check_time` int(10) NOT NULL COMMENT '盘点时间',
  `stock_check_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '盘点状态，1 已盘点，2 待盘点',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`stock_check_id`),
  KEY `warehouse_id` (`warehouse_id`,`stock_check_amount`,`stock_check_state`,`admin_id`,`stock_check_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库存盘点表';

DROP TABLE IF EXISTS `dberp_stock_check_goods`;
CREATE TABLE IF NOT EXISTS `dberp_stock_check_goods` (
  `stock_check_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_check_id` int(11) NOT NULL,
  `stock_check_pre_goods_num` int(11) NOT NULL,
  `stock_check_aft_goods_num` int(11) NOT NULL,
  `stock_check_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_number` varchar(30) NOT NULL,
  `goods_spec` varchar(100) NOT NULL,
  `goods_unit` varchar(20) NOT NULL,
  PRIMARY KEY (`stock_check_goods_id`),
  KEY `stock_check_id` (`stock_check_id`,`stock_check_goods_amount`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='盘点商品表';

DROP TABLE IF EXISTS `dberp_stock_transfer`;
CREATE TABLE IF NOT EXISTS `dberp_stock_transfer` (
  `transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_sn` varchar(50) NOT NULL COMMENT '调拨单号',
  `transfer_in_warehouse_id` int(11) NOT NULL COMMENT '入库id',
  `transfer_out_warehouse_id` int(11) NOT NULL COMMENT '出库id',
  `transfer_add_time` int(10) NOT NULL COMMENT '添加时间',
  `transfer_finish_time` int(10) DEFAULT NULL COMMENT '完成时间',
  `transfer_info` varchar(500) DEFAULT NULL COMMENT '调拨备注',
  `transfer_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '调拨状态，0 待调拨，1 已调拨',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`transfer_id`),
  KEY `transfer_sn` (`transfer_sn`,`transfer_in_warehouse_id`,`transfer_out_warehouse_id`,`admin_id`,`transfer_state`),
  KEY `transfer_add_time` (`transfer_add_time`),
  KEY `transfer_finish_time` (`transfer_finish_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库间调拨基本表';

DROP TABLE IF EXISTS `dberp_stock_transfer_goods`;
CREATE TABLE IF NOT EXISTS `dberp_stock_transfer_goods` (
  `transfer_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `in_warehouse_id` int(11) NOT NULL,
  `out_warehouse_id` int(11) NOT NULL,
  `transfer_goods_num` int(11) NOT NULL,
  `transfer_goods_state` tinyint(1) NOT NULL DEFAULT '0',
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_number` varchar(30) NOT NULL,
  `goods_spec` varchar(100) DEFAULT NULL,
  `goods_unit` varchar(20) NOT NULL,
  PRIMARY KEY (`transfer_goods_id`),
  KEY `transfer_id` (`transfer_id`,`in_warehouse_id`,`out_warehouse_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库间调拨商品表';

DROP TABLE IF EXISTS `dberp_supplier`;
CREATE TABLE IF NOT EXISTS `dberp_supplier` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_category_id` int(11) NOT NULL,
  `supplier_code` varchar(30) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `supplier_sort` int(11) NOT NULL DEFAULT '255',
  `supplier_address` varchar(255) DEFAULT NULL,
  `supplier_contacts` varchar(30) DEFAULT NULL,
  `supplier_phone` varchar(20) DEFAULT NULL,
  `supplier_telephone` varchar(20) DEFAULT NULL,
  `supplier_bank` varchar(100) DEFAULT NULL,
  `supplier_bank_account` varchar(30) DEFAULT NULL,
  `supplier_tax` varchar(30) DEFAULT NULL,
  `supplier_email` varchar(30) DEFAULT NULL,
  `supplier_info` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL DEFAULT '0',
  `region_values` varchar(100) NOT NULL,
  PRIMARY KEY (`supplier_id`),
  KEY `dberp_supplier_index` (`supplier_name`,`supplier_code`,`supplier_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_supplier_category`;
CREATE TABLE IF NOT EXISTS `dberp_supplier_category` (
  `supplier_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_category_code` varchar(30) NOT NULL,
  `supplier_category_name` varchar(100) NOT NULL,
  `supplier_category_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`supplier_category_id`),
  KEY `dberp_supplier_category_index` (`supplier_category_code`,`supplier_category_name`,`supplier_category_sort`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_system`;
CREATE TABLE IF NOT EXISTS `dberp_system` (
  `sys_id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_name` varchar(30) NOT NULL,
  `sys_body` text,
  `sys_type` varchar(15) NOT NULL,
  PRIMARY KEY (`sys_id`),
  KEY `dberp_sys_index` (`sys_name`,`sys_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统信息表';

DROP TABLE IF EXISTS `dberp_unit`;
CREATE TABLE IF NOT EXISTS `dberp_unit` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(50) NOT NULL,
  `unit_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`unit_id`),
  KEY `unit_name` (`unit_name`,`admin_id`),
  KEY `dberp_unit_unit_sort_index` (`unit_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='计量单位';

DROP TABLE IF EXISTS `dberp_warehouse`;
CREATE TABLE IF NOT EXISTS `dberp_warehouse` (
  `warehouse_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_sn` varchar(30) NOT NULL,
  `warehouse_name` varchar(100) NOT NULL,
  `warehouse_contacts` varchar(50) DEFAULT NULL,
  `warehouse_phone` varchar(30) DEFAULT NULL,
  `warehouse_sort` int(11) NOT NULL DEFAULT '255',
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`warehouse_id`),
  KEY `warehouse_sn` (`warehouse_sn`,`warehouse_name`,`admin_id`),
  KEY `waerehouse_sort` (`warehouse_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仓库数据表';

DROP TABLE IF EXISTS `dberp_warehouse_goods`;
CREATE TABLE IF NOT EXISTS `dberp_warehouse_goods` (
  `warehouse_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `warehouse_goods_stock` int(11) NOT NULL,
  PRIMARY KEY (`warehouse_goods_id`),
  KEY `dberp_warehouse_goods_index` (`warehouse_id`,`goods_id`,`warehouse_goods_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `dberp_ex_warehouse_order`;
CREATE TABLE IF NOT EXISTS `dberp_ex_warehouse_order` (
    `ex_warehouse_order_id` int(11) NOT NULL AUTO_INCREMENT,
    `warehouse_id` int(11) NOT NULL,
    `ex_warehouse_order_sn` varchar(50) NOT NULL,
    `ex_warehouse_order_state` tinyint(1) NOT NULL DEFAULT '6',
    `ex_warehouse_order_info` varchar(255) DEFAULT NULL,
    `ex_warehouse_order_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `ex_warehouse_order_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `ex_warehouse_order_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `ex_add_time` int(10) NOT NULL,
    `admin_id` int(11) NOT NULL,
    PRIMARY KEY (`ex_warehouse_order_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `ex_warehouse_order_state` (`ex_warehouse_order_state`),
    KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='其他出库表';

DROP TABLE IF EXISTS `dberp_ex_warehouse_order_goods`;
CREATE TABLE IF NOT EXISTS `dberp_ex_warehouse_order_goods` (
    `ex_warehouse_order_goods_id` int(11) NOT NULL AUTO_INCREMENT,
    `ex_warehouse_order_id` int(11) NOT NULL,
    `warehouse_id` int(11) NOT NULL,
    `warehouse_goods_ex_num` int(11) NOT NULL,
    `warehouse_goods_price` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `warehouse_goods_tax` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `warehouse_goods_amount` decimal(19,4) NOT NULL DEFAULT '0.0000',
    `goods_id` int(11) NOT NULL,
    `goods_name` varchar(100) NOT NULL,
    `goods_number` varchar(30) NOT NULL,
    `goods_spec` varchar(100) DEFAULT NULL,
    `goods_unit` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`ex_warehouse_order_goods_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `ex_warehouse_order_id` (`ex_warehouse_order_id`),
    KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='其他出库商品表';