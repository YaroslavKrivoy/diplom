<?php
namespace Webfitters\Checkout\Observer\Shipment;

class Save implements \Magento\Framework\Event\ObserverInterface {

    protected $order;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $order
    ) {
        $this->order = $order;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
    	$cost = 0;
        $shipment = $observer->getShipment();
        foreach($shipment->getPackages() as $package){
        	$params = ((object)((object)$package)->params);
        	$cost += floatval($params->container_charge);        	
        }
        if($cost > 0){
        	$order = $shipment->getOrder();
        	$oldb = $order->getBaseShippingAmount();
        	$old = $order->getShippingAmount();
        	$order->setBaseShippingAmount($cost);
        	$order->setShippingAmount($cost);
        	$order->setBaseGrandTotal(($order->getBaseGrandTotal() - $oldb) + $cost);
        	$order->setGrandTotal(($order->getGrandTotal() - $old) + $cost);
        	$order->save();
            $details = $this->order->create()->load($order->getId());
            foreach($details->getInvoiceCollection() as $invoice){
                $invoice->setBaseShippingAmount($order->getBaseShippingAmount());
                $invoice->setShippingAmount($order->getShippingAmount());
                $invoice->setBaseGrandTotal($order->getBaseGrandTotal());
                $invoice->setGrandTotal($order->getGrandTotal());
                $invoice->save();
            }
        }
        
    }

}