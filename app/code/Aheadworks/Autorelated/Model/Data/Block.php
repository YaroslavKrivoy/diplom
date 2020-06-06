<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Data;

use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Block data model
 *
 * @codeCoverageIgnore
 */
class Block extends AbstractExtensibleObject implements BlockInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        return $this->_get(self::RULE);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->_get(self::PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setRule($rule)
    {
        return $this->setData(self::RULE, $rule);
    }

    /**
     * {@inheritdoc}
     */
    public function setProducts($products)
    {
        return $this->setData(self::PRODUCTS, $products);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Autorelated\Api\Data\BlockExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
