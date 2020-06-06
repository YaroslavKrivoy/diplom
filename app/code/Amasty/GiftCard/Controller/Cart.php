<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Controller;

use Amasty\GiftCard\Model\Repository\QuoteRepository;
use Magento\Framework\Controller\Result\RawFactory;

abstract class Cart extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Amasty\GiftCard\Model\Quote
     */
    protected $quoteGiftCard;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote
     */
    protected $quoteGiftCardResource;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var QuoteRepository
     */
    protected $giftCardQuoteRepository;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $helperData;

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
        \Amasty\GiftCard\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->accountModel = $accountModel;
        $this->accountResourceModel = $accountResourceModel;
        $this->escaper = $escaper;
        $this->quoteGiftCard = $quoteGiftCard;
        $this->quoteGiftCardResource = $quoteGiftCardResource;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->quoteRepository = $quoteRepository;
        $this->resultRawFactory = $resultRawFactory;
        $this->giftCardQuoteRepository = $giftCardQuoteRepository;
        $this->helperData = $helperData;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function updateTotalsInQuote(\Magento\Quote\Model\Quote $quote)
    {
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->setDataChanges(true);
        $this->quoteRepository->save($quote);
    }
}
