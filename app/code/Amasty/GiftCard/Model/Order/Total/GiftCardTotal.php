<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Order\Total;

use Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory;

trait GiftCardTotal
{
    /**
     * @var CollectionFactory
     */
    private $quoteGiftCardCollectionFactory;

    public function __construct(CollectionFactory $quoteGiftCardCollectionFactory)
    {
        $this->quoteGiftCardCollectionFactory = $quoteGiftCardCollectionFactory;
    }

    /**
     * @param $objectWithOrder
     * @return $this
     */
    public function collectGiftTotals($objectWithOrder)
    {
        $quoteId = $objectWithOrder->getOrder()->getQuoteId();
        $giftCards = $this->quoteGiftCardCollectionFactory->create()->getGiftCardsByQuoteId($quoteId);

        foreach ($giftCards as $giftCard) {
            if ($giftCard->getBaseGiftAmount()) {
                $objectWithOrder->setGrandTotal(
                    $objectWithOrder->getGrandTotal() - $giftCard->getGiftAmount()
                );
                $objectWithOrder->setBaseGrandTotal(
                    $objectWithOrder->getBaseGrandTotal() - $giftCard->getBaseGiftAmount()
                );
            }
        }

        return $objectWithOrder;
    }
}
