<?php
namespace Webfitters\Checkout\Block\Adminhtml\Order\Create\Search;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid {

	protected function _prepareColumns(){
		$columns = parent::_prepareColumns();
		$this->addColumn(
            'qty weight',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Weight'),
                'renderer' => \Webfitters\Checkout\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Weight::class,
                'name' => 'qty_weight',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => '',
                'index' => 'qty'
            ]
        );
		return $columns;
	}

}