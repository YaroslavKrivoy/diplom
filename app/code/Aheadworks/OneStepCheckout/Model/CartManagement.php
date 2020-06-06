<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model;

use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\Quote;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Reward\Plugin\TotalsCollectorFactory
    as RewardTotalsCollectorPluginFactory;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\GiftCardAccount\Plugin\TotalsCollectorFactory
    as GiftCardAccountTotalsCollectorPluginFactory;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\CustomerBalance\Plugin\TotalsCollectorFactory
    as CustomerBalanceTotalsCollectorPluginFactory;

/**
 * Class CartManagement
 *
 * @package Aheadworks\OneStepCheckout\Model
 */
class CartManagement
{
    /**
     * Flag for correct resetting discounts only if necessary
     */
    const AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG = 'aw_osc_quote_discounts_have_been_reset_flag';

    /**
     * @var RewardTotalsCollectorPluginFactory
     */
    private $rewardTotalsCollectorPluginFactory;

    /**
     * @var GiftCardAccountTotalsCollectorPluginFactory
     */
    private $giftCardAccountTotalsCollectorPluginFactory;

    /**
     * @var CustomerBalanceTotalsCollectorPluginFactory
     */
    private $customerBalanceTotalsCollectorPluginFactory;

    /**
     * @param RewardTotalsCollectorPluginFactory $rewardTotalsCollectorPluginFactory
     * @param GiftCardAccountTotalsCollectorPluginFactory $giftCardAccountTotalsCollectorPluginFactory
     * @param CustomerBalanceTotalsCollectorPluginFactory $customerBalanceTotalsCollectorPluginFactory
     */
    public function __construct(
        RewardTotalsCollectorPluginFactory $rewardTotalsCollectorPluginFactory,
        GiftCardAccountTotalsCollectorPluginFactory $giftCardAccountTotalsCollectorPluginFactory,
        CustomerBalanceTotalsCollectorPluginFactory $customerBalanceTotalsCollectorPluginFactory
    ) {
        $this->rewardTotalsCollectorPluginFactory = $rewardTotalsCollectorPluginFactory;
        $this->giftCardAccountTotalsCollectorPluginFactory = $giftCardAccountTotalsCollectorPluginFactory;
        $this->customerBalanceTotalsCollectorPluginFactory = $customerBalanceTotalsCollectorPluginFactory;
    }

    /**
     * Reset quote discount amounts for correct recalculation of totals
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function resetAppliedDiscounts(
        TotalsCollector $totalsCollector,
        Quote $quote
    ) {
        if (empty($quote->getData(self::AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG))) {
            try {
                $rewardTotalsCollectorPlugin = $this->rewardTotalsCollectorPluginFactory->create();
                $rewardTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
            }
            try {
                $giftCardAccountTotalsCollectorPlugin = $this->giftCardAccountTotalsCollectorPluginFactory->create();
                $giftCardAccountTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
            }
            try {
                $customerBalanceTotalsCollectorPlugin = $this->customerBalanceTotalsCollectorPluginFactory->create();
                $customerBalanceTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
            }
            $quote->setData(self::AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG, true);
        }

        return $this;
    }
}
