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

namespace Install\Validator;

use Laminas\I18n\Translator\Translator;
use Laminas\Validator\AbstractValidator;

class DatabaseValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const NOT_CON       = 'notCon';
    const NOT_INNODB    = 'notInnodb';

    protected $messageTemplates = [];

    public function __construct($options = null)
    {
        $trans = new Translator();
        $this->messageTemplates = [
            self::NOT_SCALAR    => $trans->translate("这不是一个标准输入值"),
            self::NOT_CON       => $trans->translate("数据库连接失败，请检查您的数据库连接信息"),
            self::NOT_INNODB    => $trans->translate("InnoDB类型必须开启，DBShop系统才能正常安装，有些环境默认没有开启InnoDB，请手动进行开启")
        ];

        parent::__construct($options);
    }

    public function isValid($value, $context=null)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        try {
            $dsn = "mysql:host={$context['dbHost']};port={$context['dbPort']};dbname={$context['dbName']}";
            $db  = new \PDO($dsn, $context['dbUser'], $context['dbPassword']);
        } catch (\Exception $e) {
            $this->error(self::NOT_CON);
            return false;
        }

        $sth = $db->prepare('SHOW ENGINES');
        $sth->execute();
        $enginesArray = $sth->fetchAll();
        $innodbState = false;
        if(is_array($enginesArray) and !empty($enginesArray)) {
            foreach($enginesArray as $value) {
                if(strtolower($value['Engine']) == 'innodb') {
                    $supportState = strtolower($value['Support']);
                    if($supportState == 'yes' or $supportState == 'default') {
                        $innodbState = true;
                        break;
                    }
                }
            }
        }
        if (!$innodbState) {
            $this->error(self::NOT_INNODB);
            return false;
        }

        return true;
    }
}