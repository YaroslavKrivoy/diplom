<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml;

use Magento\Backend\App\Action;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;
use MageWorx\OrderEditor\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class AbstractAction extends Action
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var ShippingModel
     */
    protected $shipping;

    /**
     * @var PaymentModel
     */
    protected $payment;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RawFactory
     */
    protected $resultFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param Quote $quote
     * @param Order $order
     * @param ShippingModel $shipping
     * @param PaymentModel $payment
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Quote $quote,
        Order $order,
        ShippingModel $shipping,
        PaymentModel $payment
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory     = $resultFactory;
        $this->context           = $context;
        $this->helper            = $helper;
        $this->scopeConfig       = $scopeConfig;
        $this->quote             = $quote;
        $this->order             = $order;
        $this->shipping          = $shipping;
        $this->payment           = $payment;
    }

    /**
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
        try {
            $response = [
                'result' => $this->getResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error'  => $e->getMessage(),
                'status' => false
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create();
        $result->setContents(json_encode($response));

        return $result;
    }

    /**
     * @return Quote
     * @throws \Exception
     */
    protected function getQuote()
    {
        return $this->quote;
    }

    /**
     * @return Quote
     * @throws \Exception
     */
    protected function loadQuote()
    {
        $quoteId = $this->getOrder()->getQuoteId();
        $this->quote->loadByIdWithoutStore($quoteId);

        return $this->quote;
    }

    /**
     * @return Order
     * @throws \Exception
     */
    protected function loadOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $this->order->load($id);
        if (!$this->order->getEntityId()) {
            throw new \Exception('Can not load order');
        }
        $this->helper->setOrder($this->order);

        return $this->order;
    }

    /**
     * @return Order
     * @throws \Exception
     */
    protected function getOrder()
    {
        return $this->order;
    }

    /**
     * @return ShippingModel
     */
    protected function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @return string
     */
    protected function getResultHtml()
    {
        if (!$this->getRequest()->getParam('skip_save', false)) {
            $this->update();
        }

        $this->prepareObjects();

        //update shipping
//        if ($this->needUpdateShippingInfo()) {
//            if ($this->getIsAllowAutoRecalculateShipping()) {
//                $this->updateShippingInfo();
//            }
//        }

        //update payment
        $this->getOrder()->syncQuote();

        $this->recalculateTotals();

        return $this->prepareResponse();
    }

    /**
     * @return void
     */
    abstract protected function update();

    /**
     * @return string
     */
    abstract protected function prepareResponse();

    /**
     * @return void
     */
    protected function recalculateTotals()
    {
        $order = $this->getOrder();
        $order->collectOrderTotals();
        $order->updatePayment();
    }

    /**
     * @return void
     */
    protected function prepareObjects()
    {
        $this->loadOrder();
        $this->getShipping()->setQuote($this->loadQuote());
    }

    /**
     * @return bool
     */
    protected function needUpdateShippingInfo()
    {
        return !$this->getOrder()->getIsVirtual()
            && ($this->getShipping()->isNotAvailable() || $this->getShipping()->isTotalChanged());
    }

    /**
     * @return void
     */
    protected function updateShippingInfo()
    {
        $this->shipping->recollectShippingAmount();
    }

    /**
     * @return bool
     */
    protected function getIsAllowAutoRecalculateShipping()
    {
        return $this->helper->getIsAllowAutoRecalculateShipping();
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::actions_edit');
    }
}
