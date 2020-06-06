<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Api\Data;

/**
 * Interface GiftMessageConfigInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface GiftMessageConfigInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const IS_ENABLED = 'is_enabled';
    const ITEM_ID = 'item_id';
    /**#@-*/

    /**
     * Retrieve is enabled flag
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Set is enabled
     *
     * @param bool $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * Retrieve item id
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item id
     *
     * @param int|null $itemId
     * @return $this
     */
    public function setItemId($itemId);
}
