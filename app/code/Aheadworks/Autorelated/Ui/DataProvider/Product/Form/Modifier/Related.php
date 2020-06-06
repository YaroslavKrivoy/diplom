<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class Related
 *
 * @package Aheadworks\Autorelated\Ui\DataProvider\Product\Form\Modifier
 */
class Related extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $attributeCode = 'aw_arp_override_native';

        $arpContainerPath = $this->arrayManager->findPath(
            static::CONTAINER_PREFIX . $attributeCode,
            $meta
        );
        $relatedPath = $this->arrayManager->findPath('related', $meta);

        if ($arpContainerPath && $relatedPath) {
            $moveData = $this->arrayManager->get($arpContainerPath, $meta);
            $moveData['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_PRODUCT;
            $meta = $this->arrayManager->remove($arpContainerPath, $meta);

            $meta = $this->arrayManager->set(
                'related/children/' . static::CONTAINER_PREFIX . $attributeCode,
                $meta,
                $moveData
            );
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
