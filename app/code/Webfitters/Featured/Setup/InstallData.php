<?php
namespace Webfitters\Featured\Setup;

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
		$boolean = [
	        'input' => 'boolean',
	        'type' => 'int',
	        'required' => false,
	        'unique' => false,
	        'visible' => true,
	        'user_defined' => true
		];
		$eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'featured', array_merge($boolean, ['label' => 'Featured?']));
		$eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'featured', [
			'type' => 'int',
			'input' => 'boolean',
			'label' => 'Featured?',
			'group' => 'Content',
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0'
		]);
		$eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'featured_description', [
			'type' => 'text',
			'label' => 'Featured description',
			'group' => 'Content',
			'visible' => true,
			'required' => false,
			'user_defined' => false,
			'default' => ''
		]);
	}

}