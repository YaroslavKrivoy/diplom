<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeCurrentValue implements ObserverInterface
{
    /**
     * @var \Amasty\GiftCard\Model\AccountFactory
     */
    protected $accountModel;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Account
     */
    protected $accountResourceModel;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    public function __construct(
        \Amasty\GiftCard\Model\AccountFactory $accountModel,
        \Amasty\GiftCard\Model\ResourceModel\Account $accountResourceModel,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory
    ) {
        $this->accountModel = $accountModel;
        $this->accountResourceModel = $accountResourceModel;
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $quoteId = $quote->getId();
        $giftCards = $this->giftQuoteCollectionFactory->create()->getGiftCardsByQuoteId($quoteId);

        /** @var \Amasty\GiftCard\Model\Quote $giftCard */
        foreach ($giftCards as $giftCard) {
            $model = $this->accountModel->create();
            $this->accountResourceModel->load($model, $giftCard->getAccountId());
            $model->setCurrentValue($model->getCurrentValue() - $giftCard->getBaseGiftAmount());
            $this->accountResourceModel->save($model);
        }
    }
}
