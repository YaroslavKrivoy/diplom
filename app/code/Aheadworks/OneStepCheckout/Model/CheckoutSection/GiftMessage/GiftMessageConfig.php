<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessageConfig
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage
 */
class GiftMessageConfig extends AbstractSimpleObject implements GiftMessageConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->_get(self::IS_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
}
