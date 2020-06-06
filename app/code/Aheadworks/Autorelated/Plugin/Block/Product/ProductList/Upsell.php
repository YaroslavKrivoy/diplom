<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Plugin\Block\Product\ProductList;

use Aheadworks\Autorelated\Plugin\Block\AbstractHidingPlugin;
use Aheadworks\Autorelated\Model\BlockReplacementManager;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Upsell
 * @package Aheadworks\Autorelated\Plugin\Block\Product\ProductList
 */
class Upsell extends AbstractHidingPlugin
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
     * Check if ARP replaces current block
     *
     * @param AbstractBlock $block
     * @return bool
     */
    protected function isNeedToHideBlock($block)
    {
        return $this->blockReplacementManager->getIsArpUsedInsteadFlag($block);
    }

    /**
     * Returns empty identities array if block is hided
     *
     * @param AbstractBlock $subject
     * @param \Closure $proceed
     * @param array $params
     * @return array
     */
    public function aroundGetIdentities(
        AbstractBlock $subject,
        \Closure $proceed,
        $params = []
    ) {
        if ($this->isNeedToHideBlock($subject)) {
            return [];
        } else {
            return $proceed($params);
        }
    }
}
