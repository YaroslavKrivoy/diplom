<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\RuleFactory;
use Amasty\GiftCard\Api\CodeSetRepositoryInterface;

/**
 * Coupon management object.
 */
class GiftCardManagement implements \Amasty\GiftCard\Api\GiftCardManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Amasty\GiftCard\Model\AccountFactory
     */
    private $accountFactory;

    /**
     * @var \Amasty\GiftCard\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Amasty\GiftCard\Api\QuoteRepositoryInterface
     */
    private $giftQuoteRepository;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote
     */
    private $quoteResource;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Amasty\GiftCard\Api\CodeRepositoryInterface
     */
    private $codeRepository;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollection;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var CodeSetRepositoryInterface
     */
    private $codeSetRepository;

    private $codes = [];

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Amasty\GiftCard\Model\AccountFactory $accountFactory,
        \Amasty\GiftCard\Model\QuoteFactory $quoteFactory,
        \Amasty\GiftCard\Api\QuoteRepositoryInterface $giftQuoteRepository,
        \Amasty\GiftCard\Model\ResourceModel\Quote $quoteResource,
        \Magento\Framework\Escaper $escaper,
        \Amasty\GiftCard\Api\CodeRepositoryInterface $codeRepository,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        RuleFactory $ruleFactory,
        CodeSetRepositoryInterface $codeSetRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->accountFactory = $accountFactory;
        $this->quoteFactory = $quoteFactory;
        $this->giftQuoteRepository = $giftQuoteRepository;
        $this->quoteResource = $quoteResource;
        $this->escaper = $escaper;
        $this->codeRepository = $codeRepository;
        $this->quoteCollection = $quoteCollection;
        $this->ruleFactory = $ruleFactory;
        $this->codeSetRepository = $codeSetRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $giftCard)
    {
        if (!$giftCard) {
            throw new LocalizedException(__('Coupon with specified code  "%1" not valid.', $giftCard));
        }

        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $isValid = $this->validateCode($quote, $giftCard);

        try {
            /** @var \Amasty\GiftCard\Model\Account $accountModel */
            $accountModel = $this->accountFactory->create()
                ->loadByCode($giftCard);
            if ((bool)!$accountModel->getData() || !$isValid) {
                throw new LocalizedException(
                    __('Coupon code "%1" cannot be applied to the cart because it does not meet certain conditions. 
                        Please check the details and try again or contact us for assistance.', $giftCard)
                );
            }

            if ($accountModel->canApplyCardForQuote($quote)) {
                /** @var \Amasty\GiftCard\Model\Quote $quoteGiftCard */
                $quoteGiftCard = $this->quoteFactory->create();
                $this->quoteResource->load($quoteGiftCard, $cartId, 'quote_id');
                $subtotal = $quoteGiftCard->getSubtotal($quote);

                if ($this->isCodeAlreadyInQuote($accountModel, $cartId)) {
                    throw new LocalizedException(__('This gift card account is already in the quote.'));
                } elseif ($quoteGiftCard->getGiftAmount() && $subtotal == $quoteGiftCard->getGiftAmount()) {
                    throw new LocalizedException(__('Gift card can\'t be applied. Maximum discount reached.'));
                } else {
                    $quoteGiftCard->unsetData($quoteGiftCard->getIdFieldName());
                    $quoteGiftCard->setQuoteId($cartId);
                    $quoteGiftCard->setCodeId($accountModel->getCodeId());
                    $quoteGiftCard->setAccountId($accountModel->getId());

                    $this->giftQuoteRepository->save($quoteGiftCard);
                    $this->updateTotalsInQuote($quote);
                }
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $giftCard;
    }

    /**
     * @param \Amasty\GiftCard\Model\Account $accountModel
     * @param $cartId
     *
     * @return bool
     */
    public function isCodeAlreadyInQuote(\Amasty\GiftCard\Model\Account $accountModel, $cartId)
    {
        $this->quoteCollection->create()->addFieldToFilter('quote_id', $cartId)
            ->addFieldToSelect('code')->each([$this, 'getCode']);

        if (in_array($accountModel->getCode(), $this->codes)) {
            return true;
        } else {
            return false;
        }
    }

    public function getCode($item)
    {
        $this->codes[] = $item->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $giftCard)
    {
        if (!$giftCard) {
            throw new LocalizedException(__('Coupon with specified code  "%1" not valid.', $giftCard));
        }

        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $codeId = $this->codeRepository->getIdByCode($giftCard);
            /** @var \Amasty\GiftCard\Model\Quote $quoteModel */
            $quoteModel = $this->quoteFactory->create();
            $this->quoteResource->load($quoteModel, $codeId, 'code_id');
            $this->giftQuoteRepository->delete($quoteModel);

            $this->updateTotalsInQuote($quote);
        } catch (\Exception $e) {
            // display error message
            throw new LocalizedException(__($e->getMessage()));
        }

        return $giftCard;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function updateTotalsInQuote(\Magento\Quote\Model\Quote $quote)
    {
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->setDataChanges(true);
        $this->quoteRepository->save($quote);

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param string $giftCard
     *
     * @return bool
     */
    public function validateCode(\Magento\Quote\Model\Quote $quote, $giftCard)
    {
        $isValid = true;
        $codeModel = $this->codeRepository->getByCode($giftCard);
        $codeSetId = $codeModel->getCodeSetId();

        if ($codeSetId) {
            $codeSet = $this->codeSetRepository->getById($codeSetId);
            $codeSetConditions = $codeSet->getConditions();

            /** @var \Magento\SalesRule\Model\Rule $rule */
            $rule = $this->ruleFactory->create();
            $rule->setConditionsSerialized($codeSetConditions);
            $rule->getConditions();
            if (current($quote->getAllVisibleItems())) {
                $isValid = $rule->validate(current($quote->getAllVisibleItems())->getAddress());
            } else {
                $isValid = false;
            }
        }

        return $isValid;
    }
}
