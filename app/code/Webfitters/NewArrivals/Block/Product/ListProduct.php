<?php
namespace Webfitters\NewArrivals\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct {

	protected function _getProductCollection() {
		//parent::_getProductCollection();
		if(!$this->_productCollection){
			
			$this->_productCollection = $this->getLayer()->getProductCollection()
				->addAttributeToFilter(array(
					array('attribute' => 'news_from_date', 'lteq' => date('Y-m-d 23:59:59'))
				), 'left')
				->addAttributeToFilter(array(
					array('attribute' => 'news_to_date', 'gteq' => date('Y-m-d 00:00:00')),
					array('attribute' => 'news_to_date', 'null' => true)
				), 'left');
		}
        return $this->_productCollection;
    }

}