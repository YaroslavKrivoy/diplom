<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory as TaxCollectionFactory;
use Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory as GiftQuoteCollectionFactory;

class GiftCard extends DefaultTotal
{
    /**
     * @var GiftQuoteCollectionFactory
     */
    private $giftQuoteCollectionFactory;

    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        TaxCollectionFactory $ordersFactory,
        GiftQuoteCollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
    }

    public function getAmount()
    {
        $quoteId = $this->getOrder()->getQuoteId();
        $giftsQuoteCollection = $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);
        $giftAmount = 0;
        $giftCardLabel = [];
        foreach ($giftsQuoteCollection as $giftCard) {
            $giftAmount += $giftCard->getGiftAmount();
            $giftCardLabel[] = $giftCard->getCode();
        }
        $title = __('Gift Card') . ' ' . implode(', ', $giftCardLabel);
        $this->setTitle($title);

        return -$giftAmount;
    }
}
