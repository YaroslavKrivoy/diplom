<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\CheckoutSection;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessage
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection
 */
class GiftMessage extends AbstractSimpleObject implements GiftMessageSectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderMessage()
    {
        return $this->_get(self::ORDER_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderMessage($message)
    {
        return $this->setData(self::ORDER_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemMessages()
    {
        return $this->_get(self::ITEM_MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemMessages($messages)
    {
        return $this->setData(self::ITEM_MESSAGES, $messages);
    }
}
