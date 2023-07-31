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

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Csrf;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Helper\AbstractHelper;

class AdminHelper extends AbstractHelper
{
    private $entityManager;
    private $translator;
    private $request;
    private $csrfValue = '';

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator,
        $request
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->request          = $request;
    }

    public function appType($type)
    {
        $typeArray = Common::appType($this->translator);
        return $typeArray[$type];
    }

    public function erp($name, $type = 'base', $configFile = 'config')
    {
        return Common::configValue($type, $configFile)[$name] ?? null;
    }

    /**
     * 创建get操作的CSRF Token
     * @return string
     */
    public function getCsrfValue()
    {
        if(empty($this->csrfValue)) {
            $csrf = new Csrf('queryToken');
            $csrf->setOptions(['csrf_options' => ['timeout' => 900]]);
            $this->csrfValue = $csrf->getValue();
        }
        return $this->csrfValue;
    }

    /**
     * 返回分页url的Query字符串，去除page
     * @return bool|string
     */
    public function pagesQuery()
    {
        $queryStr = $this->request->getServer()->get('QUERY_STRING');
        if(!empty($queryStr)) {
            if(strpos($queryStr, 'page=') !== false) {
                $num = strpos($queryStr, '&');
                if($num) return substr($queryStr, $num);
                else return '';
            } else return '&'.$queryStr;
        }
        return $queryStr;
    }
}