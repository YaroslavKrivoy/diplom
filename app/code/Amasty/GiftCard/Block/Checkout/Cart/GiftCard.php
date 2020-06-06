<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Block\Checkout\Cart;

class GiftCard extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    private $dataHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->checkoutSession = $checkoutSession;
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
        $this->dataHelper = $dataHelper;
    }

    public function isEnableGiftFormInCart()
    {
        return $this->dataHelper->isEnableGiftFormInCart();
    }

    public function getAppliedCodes()
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        $giftCardsWithAccount = $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);

        return $giftCardsWithAccount;
    }
}
