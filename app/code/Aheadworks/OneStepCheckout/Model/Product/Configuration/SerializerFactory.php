<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Product\Configuration;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class SerializerFactory
 * @package Aheadworks\OneStepCheckout\Model
 */
class SerializerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create serializer instance
     *
     * @return SerializerInterface
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $serializerClassName = version_compare($magentoVersion, '2.2.0', '>=')
            ? \Magento\Framework\Serialize\Serializer\Json::class
            : \Magento\Framework\Serialize\Serializer\Serialize::class;

        return $this->objectManager->create($serializerClassName);
    }
}
