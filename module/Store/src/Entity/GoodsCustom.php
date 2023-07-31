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

namespace Store\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 商品自定义
 * @ORM\Entity(repositoryClass="Store\Repository\GoodsCustomRepository")
 * @ORM\Table(name="dberp_goods_custom")
 */
class GoodsCustom extends BaseEntity
{
    /**
     * 自定义id
     * @ORM\Id()
     * @ORM\Column(name="custom_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $customId;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 自定义标题
     * @ORM\Column(name="custom_title", type="string", length=50)
     */
    private $customTitle;

    /**
     * 自定义内容
     * @ORM\Column(name="custom_content", type="string", length=200)
     */
    private $customContent;

    /**
     * @ORM\Column(name="custom_key", type="integer", length=2)
     */
    private $customKey;

    /**
     * @return mixed
     */
    public function getCustomId()
    {
        return $this->customId;
    }

    /**
     * @param mixed $customId
     */
    public function setCustomId($customId): void
    {
        $this->customId = $customId;
    }

    /**
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param mixed $goodsId
     */
    public function setGoodsId($goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return mixed
     */
    public function getCustomTitle()
    {
        return $this->customTitle;
    }

    /**
     * @param mixed $customTitle
     */
    public function setCustomTitle($customTitle): void
    {
        $this->customTitle = $customTitle;
    }

    /**
     * @return mixed
     */
    public function getCustomContent()
    {
        return $this->customContent;
    }

    /**
     * @param mixed $customContent
     */
    public function setCustomContent($customContent): void
    {
        $this->customContent = $customContent;
    }

    /**
     * @return mixed
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * @param mixed $customKey
     */
    public function setCustomKey($customKey): void
    {
        $this->customKey = $customKey;
    }

}