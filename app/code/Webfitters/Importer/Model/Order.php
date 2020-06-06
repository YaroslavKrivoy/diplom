<?php
namespace Webfitters\Importer\Model;

class Order {

	protected $product;
	protected $cartManagement;
	protected $order;
	protected $cartRepo;
	protected $store;
	protected $invoice;
	protected $credit;
	protected $refund;
	protected $shipment;
	protected $transaction;
	protected $tracking;

	public function __construct(
		\Magento\Catalog\Model\ProductFactory $product,
		\Magento\Quote\Api\CartManagementInterface $cartManagement,
		\Magento\Sales\Model\OrderFactory $order,
		\Magento\Sales\Model\Service\InvoiceService $invoice,
		\Magento\Framework\DB\Transaction $transaction,
		\Magento\Sales\Model\Order\CreditmemoFactory $credit,
		\Magento\Sales\Model\Service\CreditmemoService $refund,
		\Magento\Sales\Model\Convert\Order $shipment,
		\Magento\Quote\Api\CartRepositoryInterface $cartRepo,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Magento\Sales\Model\Order\Shipment\TrackFactory $tracking
	){
		$this->product = $product;
		$this->cartManagement = $cartManagement;
		$this->order = $order;
		$this->transaction = $transaction;
		$this->invoice = $invoice;
		$this->cartRepo = $cartRepo;
		$this->store = $store;
		$this->credit = $credit;
		$this->refund = $refund;
		$this->shipment = $shipment;
		$this->tracking = $tracking;
	}

	public function getIncrementId($id){
		$num = $id;
		while(strlen($num) < 9){
			$num = '0'.$num;
		}
		return $num;
	}

	public function create($order, $items, $customer){
		$cart_id = $this->cartManagement->createEmptyCart();
		$cart = $this->cartRepo->get($cart_id);
		$cart->setCurrency();
		$cart->setStore($this->store->getStore());
		$cart->assignCustomer($customer);
		$cart->setInventoryProcessed(true);
		$cart->setIsSuperMode(true);
		$cart->setReservedOrderId($this->getIncrementId($order->id));
		foreach($items as $item){
			$product = $this->product->create()->loadByAttribute('sku', $item->sku);
			if($product && intval($item->quantity) > 0){
				try {
					$product->setIsSuperMode(true);
					$i = $cart->addProduct($product, intval($item->quantity), 'lite');
					if(is_object($i)){
						$i->setCustomPrice(floatval($item->price));
						$i->setOriginalCustomPrice(floatval($item->price));
						$i->getProduct()->setIsSuperMode(true);
						$i->save();
					}
				} catch(\Exception $e){echo $e->getMessage().PHP_EOL;}
			}
		}
		$cart->getBillingAddress()->addData([
			'firstname'    => $order->customer_firstname,
	        'lastname'     => $order->customer_lastname,
	        'street' => $order->customer_address,
	        'city' => $order->customer_city,
	        'country_id' => 'US',
	        'region' => $order->customer_state,
	        'postcode' => $order->customer_zip,
	        'telephone' => $order->customer_phone,
	        'save_in_address_book' => 0
		]);
		$cart->getShippingAddress()->addData([
			'firstname'    => $order->shipping_firstname,
	        'lastname'     => $order->shipping_lastname,
	        'street' => $order->shipping_address1,
	        'city' => $order->shipping_city,
	        'country_id' => 'US',
	        'region' => $order->shipping_state,
	        'postcode' => $order->shipping_zip,
	        'telephone' => $order->shipping_phone,
	        'save_in_address_book' => 0
		]);
		$cart->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates()->setShippingMethod('flatrate_flatrate');
		$cart->setPaymentMethod('checkmo');
		$cart->getPayment()->importData(['method' => 'checkmo']);
		$cart->collectTotals();
		$cart->save();
		$address = $cart->getShippingAddress();
		$address->setShippingAmount(floatval($order->shipping_price));
		$address->setBaseShippingAmount(floatval($order->shipping_price));
		$address->setShippingInclTax(floatval($order->shipping_price));
		$address->setBaseShippingInclTax(floatval($order->shipping_price));
		$address->save();
		try {
			$id = $this->cartManagement->placeOrder($cart->getId());
		} catch(\Exception $e){
			$log = fopen(dirname(__FILE__).'/orders.log', 'a+');
			fwrite($log, $e->getMessage().PHP_EOL);
			fclose($log);
			return null;
		}
		$o = $this->order->create()->load($id);
		$o->setCreatedAt(date('Y-m-d H:i:s', strtotime($order->date_ordered)));
		$o->setBaseGrandTotal($o->getBaseShippingAmount() + $o->getBaseSubtotal());
		$o->setGrandTotal($o->getBaseShippingAmount() + $o->getBaseSubtotal());
		$o->save();
		return $o->getId();
	}

	public function update($order_id, $order, $items, $customer){
		$order = $this->order->create()->load($order_id);
	}

	public function cancel($order_id){
		$order = $this->order->create()->load($order_id);
		if($order->canCancel()){
			$order->cancel();
			$order->save();
		}
	}

	public function invoice($order_id){
		$order = $this->order->create()->load($order_id);
        if($order->canInvoice()) {
            $invoice = $this->invoice->prepareInvoice($order);
            $invoice->register()->pay();
            $invoice->save();
            $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();
        }
	}

	public function refund($order_id){
		$order = $this->order->create()->load($order_id);
		if($order->canCreditmemo()){
			$refund = $this->credit->createByOrder($order);
			$this->refund->refund($refund);
		}
	}

	public function ship($order_id, $o){
		$order = $this->order->create()->load($order_id);
		if($order->canShip()){
			$shipment = $this->shipment->toShipment($order);
			foreach ($order->getAllItems() as $item) {
				if (!$item->getQtyToShip() || $item->getIsVirtual()) {
			        continue;
			    }
			    $qty = $item->getQtyToShip();
			    $sitem = $this->shipment->itemToShipmentItem($item)->setQty($qty);
			    $shipment->addItem($sitem);
			}
			$shipment->register();
			$shipment->getOrder()->setIsInProcess(true);
			$shipment->save();
			$shipment->getOrder()->save();
		} else {
			$shipments = $order->getShipmentsCollection();
			if($shipments && $shipments->count() > 0){
				foreach($shipments as $shipment){
					if($shipment->getTracksCollection()->count() == 0 && $o->shipper == 'UPS') {
						$tracking = $this->tracking->create()->addData([
							'track_number' => $o->shipper_tracking,
							'carrier_code' => 'ups',
							'title' => 'UPS'
						]);
						$shipment->addTrack($tracking)->save();
					}
				}
			}
		}
	}

	public function fill($order_id){

	}

}