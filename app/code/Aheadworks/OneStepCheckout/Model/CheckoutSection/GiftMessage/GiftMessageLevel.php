<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessageLevel
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage
 */
class GiftMessageLevel extends AbstractSimpleObject implements GiftMessageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->_get(self::CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($config)
    {
        return $this->setData(self::CONFIG, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
