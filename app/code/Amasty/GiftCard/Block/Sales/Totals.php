<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Block\Sales;

use Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory as GiftQuoteCollectionFactory;
use Magento\Framework\View\Element\Template;

class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var GiftQuoteCollectionFactory
     */
    private $giftQuoteCollectionFactory;

    public function __construct(
        Template\Context $context,
        GiftQuoteCollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        if (!$parent || !method_exists($parent, 'getOrder')) {
            return $this;
        }

        $order = $parent->getOrder();

        if (!($order instanceof \Magento\Sales\Api\Data\OrderInterface)) {
            return $this;
        }
        $quoteId = $order->getQuoteId();
        $giftCardsWithAccount = $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);

        $baseAmount = 0;
        $amount = 0;
        $giftCardLabel = [];
        foreach ($giftCardsWithAccount as $quoteGiftCard) {
            if ($quoteGiftCard->getBaseGiftAmount()) {
                $baseAmount -= $quoteGiftCard->getBaseGiftAmount();
                $amount -= $quoteGiftCard->getGiftAmount();
                $giftCardLabel[] = $quoteGiftCard->getCode();
            }
        }

        if ($baseAmount < 0) {
            $giftCard = new \Magento\Framework\DataObject(
                [
                    'code' => 'amgiftcard',
                    'strong' => false,
                    'value' => $amount,
                    'base_value' => $baseAmount,
                    'label' => __('Gift Card') . ' ' . implode(', ', $giftCardLabel)
                ]
            );

            $parent->addTotalBefore($giftCard, 'grand_total');
        }

        return $this;
    }
}
