<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Model\Service;

/**
 * QuoteManager Class
 */
class QuoteManager
{
    /**
     * @var array
     */
    protected $quoteContainsSubscription = [];

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * QuoteManager constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager *Proxy
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager
    ) {
        $this->itemManager = $itemManager;
    }

    /**
     * Check whether the given quote contains a subscription item.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return bool
     */
    public function quoteContainsSubscription($quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */

        if (($quote instanceof \Magento\Quote\Api\Data\CartInterface) !== true) {
            return false;
        }

        if ($quote->getId() && isset($this->quoteContainsSubscription[$quote->getId()])) {
            return $this->quoteContainsSubscription[$quote->getId()];
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllItems() as $item) {
            if ($this->itemManager->isSubscription($item) === true) {
                if ($quote->getId()) {
                    $this->quoteContainsSubscription[$quote->getId()] = true;
                }

                return true;
            }
        }

        if ($quote->getId()) {
            $this->quoteContainsSubscription[$quote->getId()] = false;
        }

        return false;
    }

    /**
     * Mark the quote as belonging to an existing subscription. Behavior can differ for initial vs. follow-up billings.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function setQuoteExistingSubscription(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        if ($quote->getPayment() instanceof \Magento\Quote\Api\Data\PaymentInterface) {
            $quote->getPayment()->setAdditionalInformation('is_subscription_generated', 1);
        }

        return $quote;
    }

    /**
     * Check whether the given quote is an existing subscription.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return bool
     */
    public function isQuoteExistingSubscription(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        if ($quote->getPayment() instanceof \Magento\Quote\Api\Data\PaymentInterface) {
            return (int)$quote->getPayment()->getAdditionalInformation('is_subscription_generated') === 1;
        }

        return false;
    }
}
