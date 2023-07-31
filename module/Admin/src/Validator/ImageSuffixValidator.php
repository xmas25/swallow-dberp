<?php

namespace Admin\Validator;

use Laminas\I18n\Translator\Translator;
use Laminas\Validator\AbstractValidator;

/**
 * 验证图片内容是否有非法字符，验证图片后缀是否合法
 */
class ImageSuffixValidator extends AbstractValidator
{
    const ERROR_CHARACTER   = 'errorCharacter';
    const ERROR_SUFFIX      = 'errorSuffix';

    protected $messageTemplates = [];

    public function __construct($options = null)
    {
        $trans = new Translator();
        $this->messageTemplates = [
            self::ERROR_CHARACTER   => $trans->translate("图片内含有非法字符串"),
            self::ERROR_SUFFIX      => $trans->translate("错误的图片后缀")
        ];

        parent::__construct($options);
    }

    public function isValid($value)
    {
        //非法字符串验证
        $imageBody = @file_get_contents($value['tmp_name']);
        if (
            !empty($imageBody)
            && (
                stripos($imageBody, '<?php') !== false
                || stripos($imageBody, '?>') !== false
                || stripos($imageBody, 'eval') !== false
            )) {
            $this->error(self::ERROR_CHARACTER);
            return false;
        }

        //图片后缀验证
        $imageSuffix = $this->getImageSuffix($value['name']);
        if (empty($imageSuffix) || !in_array(strtolower($imageSuffix), ['jpg', 'png', 'gif', 'bmp', 'ico'])) {
            $this->error(self::ERROR_SUFFIX);
            return false;
        }

        return true;
    }

    /**
     * 获取图片文件后缀
     * @param $fileName
     * @return false|string
     */
    private function getImageSuffix($fileName){
        $pos = strripos($fileName,'.',1);
        return substr($fileName,$pos+1);
    }
}