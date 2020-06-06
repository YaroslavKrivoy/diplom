<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreditmemoSaveActionAfter implements ObserverInterface
{
    protected $_coreRegistry;
    protected $_context;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_coreRegistry = $registry;
        $this->_context = $context;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_coreRegistry->registry('upslabel_order_id') !== null) {
            $orderId = $this->_coreRegistry->registry('upslabel_order_id');
            $shipmentId = $this->_coreRegistry->registry('upslabel_shipment_id');
            $paramShipment = $observer->getEvent()->getData('request')->getParam('creditmemo', null);
            if ($paramShipment !== null
                && isset($paramShipment['infomodus_ups_label'])
                && $paramShipment['infomodus_ups_label'] == 1
            ) {
                $this->_context->getResponse()->setRedirect($this->_context->getUrl()->getUrl('infomodus_upslabel/items/edit', ['order_id' => $orderId, 'shipment_id' => $shipmentId, 'direction' => 'refund', 'redirect_path' => 'creditmemo']))->sendResponse();
                die();
            }
        }
    }
}
