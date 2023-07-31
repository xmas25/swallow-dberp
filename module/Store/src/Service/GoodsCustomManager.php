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

namespace Store\Service;

use Doctrine\ORM\EntityManager;
use Laminas\Filter\StripTags;
use Store\Entity\GoodsCustom;

class GoodsCustomManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加或编辑商品自定义信息
     * @param array $data
     * @param $goodsId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function addOrEditGoodsCustom(array $data, $goodsId): bool
    {
        for ($i=1; $i<=10; $i++) {
            $inCustom = $this->entityManager->getRepository(GoodsCustom::class)->findOneBy(['goodsId' => $goodsId, 'customKey' => $i]);
            if($inCustom) {
                if(!empty($data['customTitle'.$i]) && !empty($data['customContent'.$i])) {
                    $inCustom->valuesSet([
                        'customTitle'   => $data['customTitle'.$i],
                        'customContent' => $data['customContent'.$i]
                    ]);
                } else $this->entityManager->remove($inCustom);
                $this->entityManager->flush();
            }else {
                if(!empty($data['customTitle'.$i]) && !empty($data['customContent'.$i])) {
                    $goodsCustom = new GoodsCustom();
                    $goodsCustom->valuesSet([
                        'goodsId'       => $goodsId,
                        'customTitle'   => $data['customTitle'.$i],
                        'customContent' => $data['customContent'.$i],
                        'customKey'     => $i
                    ]);
                    $this->entityManager->persist($goodsCustom);
                    $this->entityManager->flush();
                    $this->entityManager->clear(GoodsCustom::class);
                }
            }
        }

        return true;
    }

    /**
     * 根据商品id删除，自定义信息
     * @param $goodsId
     */
    public function deleteGoodsCustomGoodsId($goodsId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(GoodsCustom::class, 'c')->where('c.goodsId = :goodsId')->setParameter('goodsId', $goodsId);

        $qb->getQuery()->execute();
    }
}