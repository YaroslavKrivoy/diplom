<?php
namespace Webfitters\Checkout\Plugin\Quote\Item;

class ToOrder {

	public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        $orderItem->setQtyWeight($item->getQtyWeight());
        $orderItem->setCaseQty($item->getCaseQty());
        return $orderItem;
    }

}