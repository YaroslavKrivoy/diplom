<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Cart;

use Magento\Checkout\Controller\Cart\UpdatePost;
use Magento\Checkout\Model\SessionFactory;
use Amasty\GiftCard\Api\QuoteRepositoryInterface as GiftCardQuoteRepositoryInterface;
use Amasty\GiftCard\Model\GiftCardManagement;
use Magento\Framework\App\Response\RedirectInterface;

class Update
{
    /**
     * @var SessionFactory
     */
    private $checkoutSession;

    /**
     * @var GiftCardQuoteRepositoryInterface
     */
    private $quoteRepositoryGiftCard;

    /**
     * @var GiftCardManagement
     */
    private $giftCardManager;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    public function __construct(
        SessionFactory $checkoutSession,
        GiftCardQuoteRepositoryInterface $quoteRepositoryGiftCard,
        GiftCardManagement $giftCardManager,
        RedirectInterface $redirect
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepositoryGiftCard = $quoteRepositoryGiftCard;
        $this->giftCardManager = $giftCardManager;
        $this->redirect = $redirect;
    }

    /**
     * @param UpdatePost $subject
     */
    public function afterExecute(UpdatePost $subject)
    {
        $quote = $this->checkoutSession->create()->getQuote();
        $quoteId = $quote->getId();

        try {
            /** @var \Amasty\GiftCard\Model\Quote $quoteGiftCard */
            $quoteGiftCard = $this->quoteRepositoryGiftCard->getByQuoteId($quoteId);
            $code = $quoteGiftCard->getCode();
            $isValid = $this->giftCardManager->validateCode($quote, $code);
            if (!$isValid) {
                $this->quoteRepositoryGiftCard->delete($quoteGiftCard);
                $this->giftCardManager->updateTotalsInQuote($quote);
            }
        } catch (\Exception $e) {
            $this->redirect->redirect($subject->getResponse(), '*/*');
        }

        $this->redirect->redirect($subject->getResponse(), '*/*');
    }
}
