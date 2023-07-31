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

namespace Admin\Data;


class Config
{
    //提交表单的token失效时间
    const POST_TOKEN_TIMEOUT = 600;

    /**
     * 数据库连接
     */
    const ERP_DATABASE_MANAGER = 'doctrine.entitymanager.orm_default';

    /**
     * 系统服务地址
     */
    const SERVICE_URL       = 'https://service.loongdom.cn/';

    /**
     * 系统服务api
     */
    const SERVICE_API_URL   = self::SERVICE_URL . 'service-api';

    /**
     * 在线更新的KEY
     */
    const PACKAGE_UPDATE_KEY_FILE = 'data/moduleData/Package/updateKey.ini';
}