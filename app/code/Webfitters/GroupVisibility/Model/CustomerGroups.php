<?php
namespace Webfitters\GroupVisibility\Model;

class CustomerGroups extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource implements \Magento\Framework\Option\ArrayInterface {
	
	protected $groups;
	
	public function __construct(\Magento\Customer\Model\ResourceModel\Group\Collection $groups){
        $this->groups = $groups;
    }
	
	public function toArray() {
		$customers = $this->groups->toOptionArray();
    	array_unshift($customers, array('value' => '', 'label' => 'Any'));
        $groups = [];
        foreach ($customers as $group) {
            $groups[$group['value']] = $group['label'];
        }
        return $groups;
    }

    final public function toOptionArray() {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = ['value' => (string)$key, 'label' => $value];
        }
        return $ret;
    }

    public function getAllOptions(){
        return $this->toOptionArray();
    }
		
}