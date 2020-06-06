<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\GiftMessage;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\GiftMessage\Helper\Message as GiftMessageHelper;

/**
 * Class Config
 * @package Aheadworks\OneStepCheckout\Model\GiftMessage
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if gift message allowed for order level
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOrderMessageAllowed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            GiftMessageHelper::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if gift message allowed for items level
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isItemsMessageAllowed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            GiftMessageHelper::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
