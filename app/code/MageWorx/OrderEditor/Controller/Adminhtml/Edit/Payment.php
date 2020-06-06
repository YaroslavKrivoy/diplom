<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Quote\Api\CartManagementInterface;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Psr\Log\LoggerInterface;

class Payment extends AbstractAction
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $onepageCheckout;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Logger for exception details
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $params;

    /**
     * Payment constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param Quote $quote
     * @param Order $order
     * @param ShippingModel $shipping
     * @param PaymentModel $payment
     * @param CartManagementInterface $cartManagement
     * @param Onepage $onepageCheckout
     * @param JsonHelper $jsonHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Quote $quote,
        Order $order,
        ShippingModel $shipping,
        PaymentModel $payment,
        CartManagementInterface $cartManagement,
        Onepage $onepageCheckout,
        JsonHelper $jsonHelper,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultFactory,
            $helper,
            $scopeConfig,
            $quote,
            $order,
            $shipping,
            $payment
        );
        $this->eventManager = $context->getEventManager();
        $this->cartManagement = $cartManagement;
        $this->onepageCheckout = $onepageCheckout;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->params = $this->getRequest()->getParams();
    }

    /**
     * @throws \Exception
     */
    protected function update()
    {
        $this->updatePaymentMethod();
    }

    /**
     * @throws \Exception
     */
    protected function updatePaymentMethod()
    {
        $this->payment->initParams($this->params);;

        $this->payment->updatePaymentMethod();
        $this->prepareDirectpostResponse();
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }

    /**
     * Prepare date for response
     *
     */
    protected function prepareDirectpostResponse()
    {
        $result = new DataObject();
        $response = $this->getResponse();

        try {
            $result->setData('success', true);
            if (isset($this->params['order_id'])) {
                $result->setData('order_id', $this->params['order_id']);
            }

            $this->eventManager->dispatch(
                'mageworx_ordereditor_directpost',
                [
                    'result' => $result,
                    'action' => $this
                ]
            );
        } catch (LocalizedException $exception) {
            $this->logger->critical($exception);
            $result->setData('error', true);
            $result->setData('error_messages', $exception->getMessage());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $result->setData('error', true);
            $result->setData(
                'error_messages',
                __('An error occurred on the server. Please try to place the order again.')
            );
        }
        if ($response instanceof Http) {
            $response->representJson($this->jsonHelper->jsonEncode($result));
        }
    }
}