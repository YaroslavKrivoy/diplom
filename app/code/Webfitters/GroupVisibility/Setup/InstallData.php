<?php
namespace Webfitters\GroupVisibility\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface {
	private $factory;

	public function __construct(\Magento\Eav\Setup\EavSetupFactory $factory) {
		$this->factory = $factory;
	}
	
	public function install(\Magento\Framework\Setup\ModuleDataSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$eavSetup = $this->factory->create(['setup' => $setup]);
		$eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'customer_visibility', [
			'label' => 'Customer Visibility',
			'attribute_set' => '4',
			'input' => 'multiselect',
			'attribute_group' => 'Product Details',
			'type' => 'text',
			'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
			'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
			'source' => 'Webfitters\GroupVisibility\Model\CustomerGroups',
			'required' => false,
			'unique' => false,
			'visible' => true,
			'user_defined' => true,
			'used_in_product_listing' => true
		]);
	}

}