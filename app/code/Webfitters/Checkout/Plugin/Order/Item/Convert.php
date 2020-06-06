<?php
namespace Webfitters\Checkout\Plugin\Order\Item;

class Convert {

	public function aroundItemToInvoiceItem(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order\Item $item){
        $invoiceItem = $proceed($item);
        $invoiceItem->setQtyWeight($item->getQtyWeight());
        $invoiceItem->setCaseQty($item->getCaseQty());
        return $invoiceItem;
    }

    public function aroundItemToShipmentItem(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order\Item $item) {
        $shipmentItem = $proceed($item);
        $shipmentItem->setQtyWeight($item->getQtyWeight());
        $shipmentItem->setCaseQty($item->getCaseQty());
        return $shipmentItem;
    }

}