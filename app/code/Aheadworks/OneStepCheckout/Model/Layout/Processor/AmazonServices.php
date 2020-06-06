<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon as AmazonStatus;

/**
 * Class AmazonServices
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class AmazonServices implements LayoutProcessorInterface
{
    /**
     * @var AmazonStatus
     */
    private $amazonStatus;

    /**
     * @param AmazonStatus $amazonStatus
     */
    public function __construct(AmazonStatus $amazonStatus)
    {
        $this->amazonStatus = $amazonStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->amazonStatus->isEnabled()) {
            $shippingConfig = &$jsLayout['components']['checkout']['children']['shippingAddress'];
            $paymentConfig = &$jsLayout['components']['checkout']['children']['paymentMethod'];
            $availableForBilling = &$jsLayout['components']['checkout']['children']['paymentMethod']
            ['children']['billingAddress']['config']['availableForMethods'];

            if ($this->amazonStatus->isPwaEnabled()) {
                $shippingConfig['component'] =
                    'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/amazon-renderer';
                $shippingConfig['children']['shipping-address-list']
                ['component'] = 'Aheadworks_OneStepCheckout/js/view/shipping-address/amazon-list';
                $paymentConfig['children']['methodList']
                ['component'] = 'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/amazon/list';

                $amazonPaymentKey = array_search('amazon_payment', $availableForBilling);
                if ($amazonPaymentKey) {
                    array_splice($availableForBilling, $amazonPaymentKey, 1);
                }
            } else {
                unset($jsLayout['components']['checkout']['children']['amazon-button-region']);
                unset($shippingConfig['children']['amazon-widget-address']);
            }
        }

        return $jsLayout;
    }
}
