<?php
namespace Webfitters\Perishable\Setup;

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
		$eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'perishable', [
			'label' => 'Is Perishable?',
	        'input' => 'boolean',
	        'type' => 'int',
	        'required' => false,
	        'unique' => false,
	        'visible' => true,
	        'user_defined' => true
		]);
	}
}