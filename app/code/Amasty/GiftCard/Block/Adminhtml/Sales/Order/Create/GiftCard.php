<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Block\Adminhtml\Sales\Order\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class GiftCard extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_amgiftcard_form');
    }

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
    }

    public function getGiftCards()
    {
        $result = [];
        $quoteId = $this->_orderCreate->getQuote()->getId();

        $giftCardsWithAccount = $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);

        foreach ($giftCardsWithAccount as $giftCard) {
            $result[$giftCard->getCodeId()] = $giftCard->getCode();
        }
        return $result;
    }
}
