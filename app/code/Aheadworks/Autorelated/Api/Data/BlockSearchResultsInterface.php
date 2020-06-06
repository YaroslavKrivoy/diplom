<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\BlockInterface;

/**
 * Interface for Autorelated block search results
 *
 * @api
 */
interface BlockSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list
     *
     * @return BlockInterface[]
     */
    public function getItems();

    /**
     * Set blocks list
     *
     * @param BlockInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
