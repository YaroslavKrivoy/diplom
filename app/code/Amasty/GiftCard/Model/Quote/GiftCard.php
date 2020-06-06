<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Store\Model\StoreManagerInterface;

class GiftCard extends AbstractTotal
{
    /**
     * @var \Amasty\GiftCard\Model\AccountFactory
     */
    protected $accountModel;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Account
     */
    protected $accountResourceModel;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    protected $giftCardLabel = [];

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    /**
     * @var \Amasty\GiftCard\Model\Repository\QuoteRepository
     */
    private $giftCardQuoteRepository;

    /**
     * @var \Amasty\GiftCard\Model\Quote
     */
    private $quote;

    public function __construct(
        \Amasty\GiftCard\Model\AccountFactory $accountModel,
        StoreManagerInterface $storeManager,
        \Amasty\GiftCard\Model\ResourceModel\Account $accountResourceModel,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Amasty\GiftCard\Model\Repository\QuoteRepository $giftCardQuoteRepository,
        \Amasty\GiftCard\Model\Quote $quote
    ) {
        $this->accountModel = $accountModel;
        $this->storeManager = $storeManager;
        $this->accountResourceModel = $accountResourceModel;
        $this->priceCurrency = $priceCurrency;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
        $this->giftCardQuoteRepository = $giftCardQuoteRepository;
        $this->quote = $quote;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!$this->dataHelper->isEnableGiftFormInCart($quote)) {
            $this->dataHelper->removeAllCards($quote);
        }

        $rate = $quote->getBaseToQuoteRate();

        $quoteId = $quote->getId();
        $giftCardsWithAccount = $this->getGiftCardsWithAccount($quoteId);

        $giftAmount = 0;
        $baseGiftAmount = 0;
        $quote->setAmastyGift(0);
        $quote->setBaseAmastyGift(0);

        $amount = $this->quote->getSubtotal($total) + $total->getDiscountAmount();
        $baseAmount = $this->quote->getBaseSubtotal($total) + $total->getBaseDiscountAmount();

        list($baseAdditionalAmount, $additionalAmount) = $this->getAdditionalAmount($total);

        if ($baseAmount > 0) {
            foreach ($giftCardsWithAccount as $giftCard) {
                $currentValue = $giftCard->getCurrentValue();
                $currentValueRate = $currentValue * $rate;
                $giftAmount += $currentValueRate;
                $baseGiftAmount += $currentValue;

                if ($amount - $giftAmount <= 0) {
                    $giftAmount = $amount;
                    $baseGiftAmount = $baseAmount;

                    //apply for tax and shipping
                    $delta = $currentValueRate - $amount;
                    $baseDelta = $currentValue - $baseAmount;
                    $giftAmount += ($additionalAmount > $delta) ? $delta : $additionalAmount;
                    $baseGiftAmount += ($baseAdditionalAmount > $baseDelta) ? $baseDelta : $baseAdditionalAmount;
                    $subtotal = $this->quote->getSubtotal($total);
                    $baseSubtotal = $this->quote->getBaseSubtotal($total);

                    $giftAmount = min($giftAmount, $subtotal - $quote->getAmastyGift());
                    $baseGiftAmount = min($baseGiftAmount, $baseSubtotal - $quote->getBaseAmastyGift());
                    $giftCard->setGiftAmount($giftAmount);
                    $giftCard->setBaseGiftAmount($baseGiftAmount);
                    $this->giftCardQuoteRepository->save($giftCard);
                    $this->setTotals($total, $giftAmount, $baseGiftAmount, $quote);
                    break;
                } elseif ($amount - $giftAmount > 0 && $currentValue && $currentValueRate) {
                    $giftCard->setGiftAmount($currentValueRate);
                    $giftCard->setBaseGiftAmount($currentValue);
                    $this->giftCardQuoteRepository->save($giftCard);
                    $this->setTotals($total, $currentValueRate, $currentValue, $quote);
                }
            }
        }

        return $this;
    }

    /**
     * @param Total $total
     * @param int|float $giftAmount
     * @param int|float $baseGiftAmount
     * @param Quote $quote
     */
    private function setTotals(Total $total, $giftAmount, $baseGiftAmount, Quote $quote)
    {
        $total->setTotalAmount($this->getCode(), -$giftAmount);
        $total->setBaseTotalAmount($this->getCode(), -$baseGiftAmount);

        $total->setAmastyGift($giftAmount);
        $total->setBaseAmastyGift($baseGiftAmount);

        $quote->setAmastyGift($quote->getAmastyGift() + $giftAmount);
        $quote->setBaseAmastyGift($quote->getBaseAmastyGift() + $baseGiftAmount);

        $total->setGrandTotal(max(0, $total->getGrandTotal() - $giftAmount));
        $total->setBaseGrandTotal(max(0, $total->getBaseGrandTotal() - $baseGiftAmount));
    }

    /**
     * Returns shipping and/or tax amounts, depends on config options.
     * @param Total $total
     * @return array
     */
    private function getAdditionalAmount(Total $total)
    {
        $baseAmount = 0;
        $amount = 0;
        if ($this->dataHelper->isAllowedToPaidForShipping()) {
            $baseAmount = $total->getData('base_shipping_amount');
            $amount = $total->getData('shipping_amount');
        }
        if ($this->dataHelper->isAllowedToPaidForTax()) {
            $baseAmount += $total->getData('base_tax_amount');
            $amount += $total->getData('tax_amount');
        }

        return [$baseAmount, $amount];
    }

    public function fetch(Quote $quote, Total $total)
    {
        $quoteId = $quote->getId();
        $giftCardsWithAccount = $this->getGiftCardsWithAccount($quoteId);

        $applyCodes = [];
        $discount = 0;
        $subtotal = $this->dataHelper->isAllowedToPaidForTax()
            ? $total->getSubtotalInclTax()
            : $total->getSubtotal();
        $subtotal += $this->dataHelper->isAllowedToPaidForShipping() ? $total->getShippingInclTax() : 0;

        /** @var \Amasty\GiftCard\Model\Quote $giftCard */
        foreach ($giftCardsWithAccount as $giftCard) {
            $giftAmount= $giftCard->getGiftAmount();
            if ($giftAmount != 0) {
                $discount += $giftAmount;
                if (!in_array($giftCard->getCode(), $applyCodes)) {
                    $applyCodes[] = $giftCard->getCode();
                }
                if ($discount >= $subtotal) {
                    $discount = $subtotal;
                    break;
                }
            }
        }
        if ($discount) {
            return [
                'code' => $this->getCode(),
                'title' => __(implode(', ', $applyCodes)),
                'value' => -$discount
            ];
        }
    }

    /**
     * @param int $quoteId
     * @return \Amasty\GiftCard\Model\ResourceModel\Quote\Collection
     */
    private function getGiftCardsWithAccount($quoteId)
    {
        return $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);
    }
}