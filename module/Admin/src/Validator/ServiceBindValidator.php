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

namespace Admin\Validator;

use Admin\Data\Common;
use Admin\Data\Config;
use Laminas\Http\Client;
use Laminas\I18n\Translator\Translator;
use Laminas\Json\Json;
use Laminas\Validator\AbstractValidator;

/**
 * 检查服务绑定信息
 * Class ServiceBindValidator
 * @package Admin\Validator
 */
class ServiceBindValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const NOT_USER      = 'notUser';
    const CODE_ERROR    = 'codeError';
    const RESPONSE_ERROR= 'responseError';
    const ERROR_MESSAGE = 'errorMessage';

    protected $messageTemplates = [];

    public function __construct($options = null)
    {
        $trans = new Translator();
        $this->messageTemplates = [
            self::NOT_SCALAR    => $trans->translate("这不是一个标准输入值"),
            self::NOT_USER      => $trans->translate("该账户已经存在"),
            self::CODE_ERROR    => $trans->translate("网站标识码不存在，请在 https://member.loongdom.cn/ 创建"),
            self::RESPONSE_ERROR=> $trans->translate("请求出错，无法绑定"),
            self::ERROR_MESSAGE => ''
        ];

        parent::__construct($options);
    }

    public function isValid($value, $context = null)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $client = new Client(Config::SERVICE_API_URL, [
            'adapter' => 'Laminas\Http\Client\Adapter\Curl',
            'curloptions' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        $client->setHeaders(['Accept' => 'application/json']);
        $client->setMethod('POST');
        $client->setParameterPost([
            'userName'  => $value,
            'systemType'=> 'dberp',
            'action'    => 'serviceBind',
            'dataStr'   => $context['code']
        ]);

        try {
            $response = $client->send();
            if ($response->isSuccess()) {
                $result = Json::decode($response->getBody(), Json::TYPE_ARRAY);
                if ($result['code'] == 200) {
                    $array = ['userName' => $value, 'key' => $result['result']['key'], 'code' => $context['code']];
                    if (isset($result['result']['url'])) $array['url'] = $result['result']['url'];

                    Common::writeConfigFile('erpService',  $array);
                } else {
                    $this->setMessage($result['message'], self::ERROR_MESSAGE);
                    $this->error(self::ERROR_MESSAGE);
                    return false;
                }
            } else {
                $this->error(self::RESPONSE_ERROR);
                return false;
            }
        } catch (\Exception $e) {
            $this->error(self::RESPONSE_ERROR);
            return false;
        }

        return true;
    }
}