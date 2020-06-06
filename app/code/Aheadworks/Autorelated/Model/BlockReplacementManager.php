<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model;

/**
 * Class BlockReplacementManager
 *
 * @package Aheadworks\Autorelated\Model
 */
class BlockReplacementManager
{
    /**
     * Flag in block data, set to true, when ARP replaces current block
     */
    const IS_ARP_USED_INSTEAD_FLAG = 'is_arp_used_instead';

    /**
     * Set ARP used instead flag
     *
     * @param mixed $block
     */
    public function setIsArpUsedInsteadFlag($block)
    {
        if ($block && is_object($block)) {
            $block->setData(
                self::IS_ARP_USED_INSTEAD_FLAG,
                true
            );
        }
    }

    /**
     * Get ARP used instead flag
     *
     * @param mixed $block
     * @return bool
     */
    public function getIsArpUsedInsteadFlag($block)
    {
        $value = false;
        if ($block && is_object($block)) {
            $value = (bool)$block->getData(self::IS_ARP_USED_INSTEAD_FLAG);
        }
        return $value;
    }
}
