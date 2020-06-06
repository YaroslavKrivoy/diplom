<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use MageWorx\OrderEditor\Model\Edit\Quote;
use Magento\Sales\Model\Order;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;

class Add extends Action
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var RawFactory
     */
    protected $resultFactory;

    /**
     * @var Quote $processor
     */
    protected $processor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Quote $processor
     * @param Order $order
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Quote $processor,
        Order $order
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory = $resultFactory;
        $this->processor = $processor;
        $this->order = $order;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $response = [
                'result' => $this->prepareResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
                'status' => false
            ];
        }

        $updateResult = new DataObject($response);
        $json = $this->prepareResponse($updateResult);
        $result = $this->resultFactory->create()->setContents($json);
        return $result;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function prepareResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();

        $formContainer = $resultPage->getLayout()
            ->getBlock('ordereditor_order_items_form_container');
        if (empty($formContainer)) {
            throw new \Exception('Can not load block');
        }

        $order = $this->getOrder();
        $orderItems = $this->getNewOrderItems();

        $formContainer->setOrder($order);
        $formContainer->setNewOrderItems($orderItems);

        return $formContainer->toHtml();
    }

    /**
     * @param string $updateResult
     * @return string
     */
    protected function prepareResponse($updateResult)
    {
        if ($updateResult) {
            $json = $updateResult->toJson();
        } else {
            $json = '{"error":"Can not get response","status":"false"}';
        }
        return "<script type=\"text/javascript\">//<![CDATA[ \r\n var iFrameResponse = " . $json . ";\r\n //]]></script>";
    }

    /**
     * @return Order
     * @throws \Exception
     */
    protected function getOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $this->order->load($id);
        if (!$this->order->getEntityId()) {
            throw new \Exception('Can not load order');
        }
        return $this->order;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item[]
     */
    protected function getNewOrderItems()
    {
        $items = $this->getRequest()->getParam('item', []);
        $order = $this->getOrder();

        return $this->processor->createNewOrderItems($items, $order);
    }
}
