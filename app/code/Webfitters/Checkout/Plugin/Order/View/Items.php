<?php
namespace Webfitters\Checkout\Plugin\Order\View;

class Items {

	public function aroundGetColumnHtml(
		\Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $subject, 
		$proceed, 
		\Magento\Framework\DataObject $item, 
		$column, 
		$field = null
	) {
		switch($column){
			case 'cases':
				if(floatval($item->getCaseQty()) > 0){
					return $item->getCaseQty();
				}
				return '';
			case 'price-lb':
				if(floatval($item->getQtyWeight()) > 0){
					return '$'.number_format($item->getRowTotal() / $item->getQtyWeight() / $item->getQtyOrdered(), 2);
				}
				return '';
			case 'weight':
				return (floatval($item->getQtyWeight()) > 0)?$item->getQtyWeight().' lbs':'';
			case 'unit':
				if(floatval($item->getQtyWeight()) > 0){
					return floatval($item->getQtyWeight()).' lbs';
				} else {
					return ($item->getWeight() * $item->getQtyOrdered()).' lbs';
				}
				return 'N/A';
		}
        return $proceed($item, $column, $field);
    }

}