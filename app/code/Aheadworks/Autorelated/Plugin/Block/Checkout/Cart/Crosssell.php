<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Plugin\Block\Checkout\Cart;

use Aheadworks\Autorelated\Plugin\Block\AbstractHidingPlugin;
use Aheadworks\Autorelated\Model\BlockReplacementManager;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Crosssell
 *
 * @package Aheadworks\Autorelated\Plugin\Block\Checkout\Cart
 */
class Crosssell extends AbstractHidingPlugin
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
}
