<?php
namespace Webfitters\Checkout\Model\Product\ResourceModel;

class TierPrice extends \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice {

	protected function _loadPriceDataColumns($columns) {
        $columns = parent::_loadPriceDataColumns($columns);
        $columns['value_lb'] = 'value_lb';
        return $columns;
    }

    protected function _loadPriceDataSelect($select) {
    	$select->columns('value_lb');
        return $select;
    }

}