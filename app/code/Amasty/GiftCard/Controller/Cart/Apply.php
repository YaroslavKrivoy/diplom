<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Controller\Cart;

use Amasty\GiftCard\Model\Repository\QuoteRepository;
use Magento\Framework\Controller\Result\RawFactory;
use Amasty\GiftCard\Model\GiftCardManagement;
use Magento\Framework\Exception\LocalizedException;

class Apply extends \Amasty\GiftCard\Controller\Cart
{
    /**
     * @var GiftCardManagement
     */
    private $giftCardManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\GiftCard\Model\AccountFactory $accountModel,
        \Amasty\GiftCard\Model\ResourceModel\Account $accountResourceModel,
        \Magento\Framework\Escaper $escaper,
        \Amasty\GiftCard\Model\QuoteFactory $quoteGiftCard,
        \Amasty\GiftCard\Model\ResourceModel\Quote $quoteGiftCardResource,
        QuoteRepository $giftCardQuoteRepository,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        RawFactory $resultRawFactory,
        \Amasty\GiftCard\Helper\Data $helperData,
        GiftCardManagement $giftCardManager
    ) {
        $this->giftCardManager = $giftCardManager;
        parent::__construct(
            $context,
            $accountModel,
            $accountResourceModel,
            $escaper,
            $quoteGiftCard,
            $quoteGiftCardResource,
            $giftCardQuoteRepository,
            $checkoutSession,
            $storeManager,
            $customerSession,
            $scopeConfig,
            $coreRegistry,
            $quoteRepository,
            $resultRawFactory,
            $helperData
        );
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data['am_giftcard_code'] !== "") {
            $code = trim($data['am_giftcard_code']);
            try {
                /** @var \Amasty\GiftCard\Model\Account $accountModel */
                $accountModel = $this->accountModel->create()
                    ->loadByCode($code);

                $quote = $this->checkoutSession->create()->getQuote();
                $quoteId = $quote->getId();

                $isValid = $this->giftCardManager->validateCode($quote, $code);
                if (!$isValid) {
                    throw new LocalizedException(
                        __('Coupon code "%1" cannot be applied to the cart because it does not meet certain conditions. 
                        Please check the details and try again or contact us for assistance.', $code)
                    );
                }

                if ($accountModel->canApplyCardForQuote($quote)) {
                    /** @var \Amasty\GiftCard\Model\Quote $quoteGiftCard */
                    $quoteGiftCard = $this->quoteGiftCard->create();
                    $this->quoteGiftCardResource->load($quoteGiftCard, $quoteId, 'quote_id');
                    $subtotal = $quoteGiftCard->getSubtotal($quote);

                    if ($this->giftCardManager->isCodeAlreadyInQuote($accountModel, $quoteId)) {
                        $this->messageManager->addErrorMessage(__('This gift card account is already in the quote.'));
                    } elseif ($quoteGiftCard->getGiftAmount() && $subtotal == $quoteGiftCard->getGiftAmount()) {
                        $this->messageManager->addErrorMessage(__('Gift card can\'t be applied. Maximum discount reached.'));
                    } else {
                        $quoteGiftCard->unsetData($quoteGiftCard->getIdFieldName());
                        $quoteGiftCard->setQuoteId($quoteId);
                        $quoteGiftCard->setCodeId($accountModel->getCodeId());
                        $quoteGiftCard->setAccountId($accountModel->getId());

                        $this->giftCardQuoteRepository->save($quoteGiftCard);
                        $this->updateTotalsInQuote($quote);

                        $this->messageManager->addSuccessMessage(
                            __('Gift Card "%1" was added.', $this->escaper->escapeHtml($code))
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('checkout/cart/');
        return;
    }
}
