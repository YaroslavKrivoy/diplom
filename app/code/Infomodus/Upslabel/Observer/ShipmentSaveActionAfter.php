<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShipmentSaveActionAfter implements ObserverInterface
{
    protected $_coreRegistry;
    protected $_context;

    /**
     * @param \Magento\Framework\App\ResponseInterface $response
     */
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
            $paramShipment = $observer->getEvent()->getData('request')->getParam('shipment', null);
            if ($paramShipment !== null
                && isset($paramShipment['infomodus_ups_label'])
                && $paramShipment['infomodus_ups_label'] == 1
            ) {
                $controller = $observer->getControllerAction();
                $controller->getResponse()->setRedirect($controller->getUrl('infomodus_upslabel/items/edit', ['order_id' => $orderId, 'shipment_id' => $shipmentId, 'direction' => 'shipment', 'redirect_path' => 'shipment']))->sendResponse();
                die();
            }
        }

        return $this;
    }
}
