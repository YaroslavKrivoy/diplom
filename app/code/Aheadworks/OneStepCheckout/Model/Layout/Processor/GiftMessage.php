<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class GiftMessage
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class GiftMessage implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $giftMessageItemPath = 'components/checkout/children/cart-items/children/details/giftMessageRendererConfig';
        $paymentOptionsChildrenPath = 'components/checkout/children/payment-options/children/giftMessage';
        $giftMessageItemLayout = $this->arrayManager->get($giftMessageItemPath, $jsLayout);

        $giftMessageOrderLayout = $giftMessageItemLayout;
        $giftMessageItemLayout['level'] = 'item';

        $giftMessageOrderLayout['dataScope'] = 'giftMessage.order';
        $giftMessageOrderLayout['level'] = 'order';

        $jsLayout = $this->arrayManager->set($giftMessageItemPath, $jsLayout, $giftMessageItemLayout);
        $jsLayout = $this->arrayManager->set($paymentOptionsChildrenPath, $jsLayout, $giftMessageOrderLayout);

        return $jsLayout;
    }
}
