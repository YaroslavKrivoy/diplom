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

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface;

/**
 * Item Manager: Run checks and fetch subscription values relative to a particular quote/order item.
 */
class ItemManager
{
    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfig;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface
     */
    protected $intervalRepository;

    /**
     * @var array
     */
    protected $intervalsByProductId = [];

    /**
     * @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[]
     */
    protected $intervalsByItemId = [];

    /**
     * Item constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper *Proxy
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig *Proxy
     * @param \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository *Proxy
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository
    ) {
        $this->helper = $helper;
        $this->productConfig = $productConfig;
        $this->currencyManager = $currencyManager;
        $this->config = $config;
        $this->intervalRepository = $intervalRepository;
    }

    /**
     * Get the subscription interval (if any) for the current item. 0 for none.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getFrequencyCount(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Check for single-option case first.
        if ($this->isSingleOptionSubscription($item)) {
            return (int)$item->getProduct()->getData('subscription_intervals');
        }

        // Check for chosen interval model.
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface) {
            return $interval->getFrequencyCount();
        }

        // Fall back to legacy detection. This probably isn't necessary anymore, but safe is good.
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            $options = $this->productConfig->getCustomOptions($item);
        } else {
            $options = $item->getProductOptions();
            $options = isset($options['options']) ? $options['options'] : [];
        }

        if (is_array($options)) {
            foreach ($options as $option) {
                if (__($option['label']) === $this->config->getSubscriptionLabel()) {
                    return $this->helper->getIntervalFromString(
                        $option['value'],
                        (string)__('Every ' . $this->getFrequencyUnit($item))
                    );
                }
            }
        }

        return 0;
    }

    /**
     * Get the subscription unit for the current item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return string
     */
    public function getFrequencyUnit(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getFrequencyUnit() !== null) {
            return $interval->getFrequencyUnit();
        }

        // Fall back to product attribute value
        return $item->getProduct()->getData('subscription_unit');
    }

    /**
     * Get the subscription length for the current item--number of billing cycles to be run. 0 for indefinite.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getLength(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getLength() !== null) {
            return $interval->getLength();
        }

        // Fall back to product attribute value
        return (int)$item->getProduct()->getData('subscription_length');
    }

    /**
     * Get the subscription adjustment price for the given item. Raw value, not converted.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return float
     */
    public function getAdjustmentPrice(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getAdjustmentPrice() !== null) {
            return $interval->getAdjustmentPrice();
        }

        // Fall back to product attribute value
        $adjustmentPrice = $item->getProduct()->getData('subscription_init_adjustment');
        if (is_numeric($adjustmentPrice)) {
            return (float)$adjustmentPrice;
        }

        return null;
    }

    /**
     * Get the subscription installment price for the given item. Raw value, not converted.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return float|null
     */
    public function getInstallmentPrice(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getInstallmentPrice() !== null) {
            return $interval->getInstallmentPrice();
        }

        // Fall back to product attribute value
        $installmentPrice = $item->getProduct()->getData('subscription_price');
        if (is_numeric($installmentPrice)) {
            return (float)$installmentPrice;
        }

        return null;
    }

    /**
     * Calculate price for a subscription item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @param int $installment
     * @param string $baseCurrency Website base currency code (convert from)
     * @param string $quoteCurrency Cart currency code (convert to)
     * @return float
     */
    public function calculatePrice(
        \Magento\Framework\Model\AbstractExtensibleModel $item,
        $installment,
        $baseCurrency = null,
        $quoteCurrency = null
    ) {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        $product = $item->getProduct();
        $price   = $product->getFinalPrice();

        // Take subscription price to start (if any); otherwise, use normal product price.
        $installmentPrice = $this->getInstallmentPrice($item);
        if ($installmentPrice !== null) {
            $price = $this->currencyManager->convertPriceCurrency(
                $installmentPrice,
                $baseCurrency,
                $quoteCurrency
            );
        }

        // If this is the first billing, add the initial adjustment fee (if any).
        $adjustmentPrice = $this->getAdjustmentPrice($item);
        if ($installment === 1 && $adjustmentPrice !== null) {
            $price += $this->currencyManager->convertPriceCurrency(
                $adjustmentPrice,
                $baseCurrency,
                $quoteCurrency
            );
        }

        // Return calculated value. Final price must not be negative.
        return max($price, 0.0);
    }

    /**
     * Get the subscription description for the given item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return string
     */
    public function getSubscriptionDescription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        return $item->getName();
    }

    /**
     * Get the subscription run count of the given item (if any).
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getSubscriptionRunCount(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        if ($item->getData('subscription') instanceof \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface) {
            // Returning one higher than recorded subscription count because most logic will occur based on the billing
            // that has yet to occur. EG, for initial purchase, run_count=0 but it's installment #1. Likewise for others
            return $item->getData('subscription')->getRunCount() + 1;
        }

        if ($this->isSubscription($item)) {
            // If item has no subscription model, must be initial order (no subscription yet), or else something else
            // unexpected happened. Default to 1 (initial billing).
            return 1;
        }

        // Otherwise, we've got a non-subscription item, zeroth installment.
        return 0;
    }

    /**
     * Get the ProductInterval for the given item (if any).
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    public function getIntervalModel(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Key is to prevent any chance of collision between order and quote items since this can be used for both.
        // Using object hash rather than a known ID because $item often isn't saved yet (no unique identifier).
        $itemKey = spl_object_hash($item);

        /**
         * If we've already found the appropriate interval model, return it straight up.
         */
        if (isset($this->intervalsByItemId[ $itemKey ])) {
            return $this->intervalsByItemId[ $itemKey ];
        }

        /**
         * If we haven't loaded intervals for the given item's product, load them.
         */
        if (!isset($this->intervalsByProductId[ $item->getProductId() ])) {
            $results = $this->intervalRepository->getIntervalsByProductId(
                $item->getProductId()
            );

            $this->intervalsByProductId[ $item->getProductId() ] = $results->getItems();
        }

        /**
         * If we have intervals, dig into the item options and try to make a match by option and value ID.
         */
        if (!empty($this->intervalsByProductId[ $item->getProductId() ])) {
            /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */
            $intervals = $this->intervalsByProductId[ $item->getProductId() ];

            if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                $interval = $this->findQuoteItemIntervalModel($item, $intervals);
            } else {
                $interval = $this->findOrderItemIntervalModel($item, $intervals);
            }

            if ($interval !== null) {
                // That's a match! Store and return it.
                $this->intervalsByItemId[ $itemKey ] = $interval;

                return $interval;
            }
        }

        // No results? Let's not do that again.
        $this->intervalsByItemId[ $itemKey ] = null;

        return null;
    }

    /**
     * Check whether the given item should be a subscription.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return bool
     */
    public function isSubscription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /**
         * Check for enabled subscription and a chosen interval
         */
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        if ((int)$item->getProduct()->getData('subscription_active') === 1
            && $this->getFrequencyCount($item) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the item's product has only one subscription option.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return bool
     */
    public function isSingleOptionSubscription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        // By definition (as we currently use it), this is a product with 'onetime' disabled and one (and only one)
        // number in the 'subscription_intervals' attribute. There may or may not be a custom option and interval model.

        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        if ((int)$item->getProduct()->getData('subscription_allow_onetime') === 0
            && !empty($item->getProduct()->getData('subscription_intervals'))
            && strpos($item->getProduct()->getData('subscription_intervals'), ',') === false) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the given item has a parent we should set the price on.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return bool
     */
    public function parentItemShouldHavePrice(\Magento\Quote\Api\Data\CartItemInterface $quoteItem)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        return $quoteItem->getParentItem() instanceof \Magento\Quote\Api\Data\CartItemInterface
            && $quoteItem->getParentItem()->getProductType() === Configurable::TYPE_CODE;
    }

    /**
     * Find an interval (if any) matching the given item.
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param $intervals
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    protected function findQuoteItemIntervalModel(\Magento\Quote\Model\Quote\Item $item, $intervals)
    {
        /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */

        $optionIds = $item->getOptionByCode('option_ids');

        if ($optionIds instanceof \Magento\Quote\Model\Quote\Item\Option) {
            // For each option and each interval, compare IDs.
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getOptionByCode('option_' . $optionId);

                if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
                    foreach ($intervals as $interval) {
                        if ($interval->getOptionId() === (int)$optionId
                            && $interval->getValueId() === (int)$option->getValue()) {
                            // That's a match!
                            return $interval;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find an interval (if any) matching the given item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @param $intervals
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    protected function findOrderItemIntervalModel(\Magento\Framework\Model\AbstractExtensibleModel $item, $intervals)
    {
        /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */

        // For order items: options[].option_value is option_type_id, option_id is option_id.
        $options = $item->getProductOptions();
        $options = isset($options['options']) ? $options['options'] : [];

        if (is_array($options)) {
            // For each option and each interval, compare IDs.
            foreach ($options as $option) {
                foreach ($intervals as $interval) {
                    $valueId = isset($option['option_value']) ? $option['option_value'] : $option['value'];

                    if (is_numeric($valueId)
                        && $interval->getOptionId() === (int)$option['option_id']
                        && $interval->getValueId() === (int)$valueId) {
                        // That's a match!
                        return $interval;
                    }
                }
            }
        }

        return null;
    }
}
