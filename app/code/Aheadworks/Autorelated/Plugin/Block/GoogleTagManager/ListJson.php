<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Plugin\Block\GoogleTagManager;

use Aheadworks\Autorelated\Model\BlockReplacementManager;

/**
 * Class ListJson
 *
 * @package Aheadworks\Autorelated\Plugin\Block\GoogleTagManager
 */
class ListJson
{
    /**
     * @var BlockReplacementManager
     */
    private $blockReplacementManager;

    /**
     * @param BlockReplacementManager $blockReplacementManager
     */
    public function __construct(
        BlockReplacementManager $blockReplacementManager
    ) {
        $this->blockReplacementManager = $blockReplacementManager;
    }

    /**
     * Disable adding static impressions data, if ARP replaces corresponding product collection
     *
     * @param mixed $subject
     * @param array $result
     * @return array | null
     */
    public function afterGetLoadedProductCollection(
        $subject,
        $result = []
    ) {
        if ($this->isNeedToDisableAddingStaticImpressions($subject)) {
            return null;
        } else {
            return $result;
        }
    }

    /**
     * Check if need to disable adding static impressions data
     *
     * @param mixed $block
     * @return bool
     */
    private function isNeedToDisableAddingStaticImpressions($block)
    {
        $listBlock = $block->getListBlock();
        return $this->blockReplacementManager->getIsArpUsedInsteadFlag($listBlock);
    }
}
