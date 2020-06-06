<?php
namespace KozakGroup\RewriteOrderEditor\Plugin\Order\View;

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
                return '--';
            case 'price-lb':
                if(floatval($item->getQtyWeight()) > 0){
                    return '$'.number_format($item->getRowTotal() / ($item->getQtyWeight()*$item->getQtyOrdered()), 2);
                }
                return '--';
			case 'unit':
				if(floatval($item->getQtyWeight()) > 0){
					return number_format($item->getQtyWeight() * $item->getQtyOrdered(), 4).' lbs';
				} else {
					return number_format($item->getWeight() * $item->getQtyOrdered(), 4).' lbs';
				}
		}
        return $proceed($item, $column, $field);
    }
}