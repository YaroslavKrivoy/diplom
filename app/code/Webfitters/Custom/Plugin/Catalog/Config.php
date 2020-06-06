<?php
namespace Webfitters\Custom\Plugin\Catalog;

class Config {

	public function aroundGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $subject, \Closure $proceed){
		$options = $proceed();
		unset($options['position']);
		return $options;
	}

}