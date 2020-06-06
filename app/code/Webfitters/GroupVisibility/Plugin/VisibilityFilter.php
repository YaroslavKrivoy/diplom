<?php
namespace Webfitters\GroupVisibility\Plugin;

class VisibilityFilter {
	
	protected $customer;
	
	public function __construct(\Magento\Customer\Model\Session $customer){
		$this->customer = $customer;
	}
	
	 public function beforeLoad(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection) {
		/*$group = $this->customer->getCustomer()->getGroupId();
		if(!$this->customer->isLoggedIn()){
			$group = 0;
		}
		$collection->addAttributeToSelect('customer_visibility')->addAttributeToFilter('customer_visibility', array('like' => '%'.$group.'%'));*/
    }
	
}