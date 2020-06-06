<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api\Data;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Catalog\Model\Product;

/**
 * Autorelated block interface
 *
 * @api
 */
interface BlockInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const RULE = 'rule';
    const PRODUCTS = 'products';
    /**#@-*/

    /**
     * Get rule
     *
     * @return RuleInterface|null
     */
    public function getRule();

    /**
     * Get products
     *
     * @return Product[]|null
     */
    public function getProducts();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Autorelated\Api\Data\BlockExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set rule
     *
     * @param RuleInterface $rule
     * @return BlockInterface
     */
    public function setRule($rule);

    /**
     * Set products
     *
     * @param Product[] $products
     * @return BlockInterface
     */
    public function setProducts($products);

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Autorelated\Api\Data\BlockExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Autorelated\Api\Data\BlockExtensionInterface $extensionAttributes
    );
}
