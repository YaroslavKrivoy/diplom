<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Plugin\Block;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class AbstractHidingPlugin
 *
 * @package Aheadworks\Autorelated\Plugin\Block
 */
abstract class AbstractHidingPlugin
{
    /**
     * Content, if no need to display block
     */
    const EMPTY_BLOCK_CONTENT = '';

    /**
     * Hide block if necessary
     *
     * @param AbstractBlock $subject
     * @param \Closure $proceed
     * @param array $params
     * @return string
     */
    public function aroundToHtml(
        AbstractBlock $subject,
        \Closure $proceed,
        $params = []
    ) {
        if ($this->isNeedToHideBlock($subject)) {
            return self::EMPTY_BLOCK_CONTENT;
        } else {
            return $proceed($params);
        }
    }

    /**
     * Check if need to hide block
     *
     * @param AbstractBlock $block
     * @return bool
     */
    abstract protected function isNeedToHideBlock($block);
}
