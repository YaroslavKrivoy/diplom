<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Api\Data;

/**
 * Interface GiftMessageInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface GiftMessageInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const CONFIG = 'config';
    const MESSAGE = 'message';
    /**#@-*/

    /**
     * Retrieve config
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface
     */
    public function getConfig();

    /**
     * Set config
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface $config
     * @return $this
     */
    public function setConfig($config);

    /**
     * Retrieve gift message
     *
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getMessage();

    /**
     * Set gift message
     *
     * @param \Magento\GiftMessage\Api\Data\MessageInterface|null $message
     * @return $this
     */
    public function setMessage($message);
}
