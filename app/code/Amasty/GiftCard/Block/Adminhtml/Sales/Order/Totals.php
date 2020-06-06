<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Block\Adminhtml\Sales\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
    }

    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Create the weee ("FPT") totals summary
     *
     * @return $this
     */
    public function initTotals()
    {
        $quoteId = $this->getSource()->getQuoteId();

        $quoteCollection = $this->giftQuoteCollectionFactory->create()->getGiftCardsWithAccount($quoteId);
        $countCode = $quoteCollection->getSize();
        foreach ($quoteCollection as $quote) {
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $this->getNameInLayout() . $quote->getCodeId(),
                    'label' => __('Gift Card (%1)', $quote->getCode()),
                    'value' => -$quote->getGiftAmount(),
                    'base_value' => -$quote->getBaseGiftAmount()
                ]
            );
            if ($countCode > 1) {
                if ($this->getBeforeCondition()) {
                    $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
                } else {
                    $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
                }
            }
        }

        return $this;
    }
}
