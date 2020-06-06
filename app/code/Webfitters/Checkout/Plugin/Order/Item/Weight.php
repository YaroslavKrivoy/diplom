<?php
namespace Webfitters\Checkout\Plugin\Order\Item;

class Weight {

	public function aroundGetQty(
		\Magento\Sales\Model\Order\Item $item,
		\Closure $proceed
	) {
		if(floatval($item->getCaseQty()) > 0){
			return floatval($item->getCaseQty());
		}
		return $proceed();
	}

	public function aroundGetWeight(
		\Magento\Sales\Model\Order\Item $item,
		\Closure $proceed
	) {
		if(floatval($item->getQtyWeight()) > 0){
			return floatval($item->getQtyWeight());
		}
		return $proceed();
	}

}