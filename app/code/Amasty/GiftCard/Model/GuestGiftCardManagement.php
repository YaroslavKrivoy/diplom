<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Coupon management class for guest carts.
 */
class GuestGiftCardManagement implements \Amasty\GiftCard\Api\GuestGiftCardManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var \Amasty\GiftCard\Api\GiftCardManagementInterface
     */
    private $gitCodeManagement;

    public function __construct(
        \Amasty\GiftCard\Api\GiftCardManagementInterface $gitCodeManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->gitCodeManagement = $gitCodeManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->gitCodeManagement->set($quoteIdMask->getQuoteId(), $couponCode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $giftCard)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->gitCodeManagement->remove($quoteIdMask->getQuoteId(), $giftCard);
    }
}
