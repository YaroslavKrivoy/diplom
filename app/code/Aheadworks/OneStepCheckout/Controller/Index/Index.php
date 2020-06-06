<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Controller\Index;

use Aheadworks\OneStepCheckout\Model\AvailabilityFlag;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Page\Initializer as PageInitializer;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;

/**
 * Class Index
 * @package Aheadworks\OneStepCheckout\Controller\Index
 */
class Index extends Action
{
    /**
     * @var AvailabilityFlag
     */
    private $availabilityFlag;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var PageInitializer
     */
    private $pageInitializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param AvailabilityFlag $availabilityFlag
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param CustomerRepository $customerRepository
     * @param PageInitializer $pageInitializer
     * @param Config $config
     */
    public function __construct(
        Context $context,
        AvailabilityFlag $availabilityFlag,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        CustomerRepository $customerRepository,
        PageInitializer $pageInitializer,
        Config $config
    ) {
        parent::__construct($context);
        $this->availabilityFlag = $availabilityFlag;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->customerRepository = $customerRepository;
        $this->pageInitializer = $pageInitializer;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->availabilityFlag->isAvailable()) {
            $message = $this->availabilityFlag->getMessage();
            if ($message) {
                $this->messageManager->addNoticeMessage(__($message));
            }
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('checkout/cart');
            return $resultRedirect;
        }

        $this->customerSession->regenerateId();
        $this->checkoutSession->setCartWasUpdated(false);
        $quote = $this->checkoutSession->getQuote();

        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $quote->assignCustomer($customer);
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $pageConfig = $resultPage->getConfig();
        if ($this->config->isDisplayTopMenu($quote->getStore()->getWebsiteId())) {
            $pageConfig->setPageLayout('checkout_with_top_menu');
        }
        $this->pageInitializer->init($resultPage);

        return $resultPage;
    }
}
