<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Edit extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_handy;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_handy = $handy;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id', null);
        $type = $this->getRequest()->getParam('direction');
        $model = [];
        if ($orderId) {
            $order = $this->_objectManager->get('Magento\Sales\Model\OrderRepository')->get($orderId);
            if ($order) {
                $intermediate = $this->_handy->intermediate($order, $type, $shipmentId);
                if (true === $intermediate) {
                    $model['handy'] = $this->_handy;
                    $model['object'] = $this->_objectManager;
                } else {
                    $this->messageManager->addErrorMessage(__('Error 10001.'));
                    $this->_redirect('infomodus_upslabel/*');
                    return;
                }
            } else {
                $this->messageManager->addErrorMessage(__('Order ID is incorrect.'));
                $this->_redirect('infomodus_upslabel/*');
                return;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Order ID is required.'));
            $this->_redirect('infomodus_upslabel/*');
            return;
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model['data'] = $data;
        }

        $this->coreRegistry->register('current_infomodus_upslabel_items', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('items_items_edit');
        $this->_view->renderLayout();
    }
}
