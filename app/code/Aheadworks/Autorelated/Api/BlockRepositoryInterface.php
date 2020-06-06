<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api;

use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Autorelated block repository interface
 *
 * @api
 */
interface BlockRepositoryInterface
{
    /**
     * Retrieve block(s) matching the specified blockType and blockPosition
     *
     * @param int $blockType
     * @param int $blockPosition
     * @param bool $allBlocks
     * @param int|null $storeId
     * @param string[] $ruleIds
     *
     * @return BlockSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        $blockType,
        $blockPosition,
        $allBlocks = false,
        $storeId = null,
        $ruleIds = []
    );
}
