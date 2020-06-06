<?php
namespace KozakGroup\RewriteOrderEditor\Plugin\Quote\Item;

class ToOrder {

	public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        if(floatval($item->getQtyWeight()) > 0){
            $orderItem->setQtyWeight($item->getWeight());
            $orderItem->setCaseQty($item->getCaseQty());
            $orderItem->setQtyOrdered($item->getCaseQty());
            $orderItem->setPrice($item->getPrice()/($item->getCaseQty()));
            $orderItem->setPriceInclTax($item->getPriceInclTax()/($item->getCaseQty()));
            $orderItem->setBasePriceInclTax($item->getBasePriceInclTax()/($item->getCaseQty()));
        } else {
            $orderItem->setQtyWeight($item->getQtyWeight());
            $orderItem->setCaseQty($item->getCaseQty());
        }
        return $orderItem;
    }

}