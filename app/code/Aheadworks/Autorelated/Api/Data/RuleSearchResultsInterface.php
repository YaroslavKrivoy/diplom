<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Interface for Autorelated rule search results
 *
 * @api
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rules list
     *
     * @return RuleInterface[]
     */
    public function getItems();

    /**
     * Set rules list
     *
     * @param RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
