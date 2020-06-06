<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrdersBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_CHECKOUT_PATH = 'mageworx_order_management/order_base/main/checkout_path';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Get checkout base path. "checkout" is default value
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_CHECKOUT_PATH);
    }
}
