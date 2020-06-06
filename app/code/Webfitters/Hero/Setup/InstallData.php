<?php
namespace Webfitters\Hero\Setup;

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
		$eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'hero_parallax', [
			'type' => 'boolean',
			'label' => 'Hero Parallax?',
			'group' => 'Content',
			'visible' => true,
			'required' => false,
			'user_defined' => false,
			'default' => '0'
		]);
		$eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'hero_image', [
			'type' => 'varchar',
			'label' => 'Hero Image',
			'group' => 'Content',
			'input' => 'image',
			'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
			'visible' => true,
			'required' => false,
			'user_defined' => false,
			'default' => ''
		]);
		$eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'hero_title', [
			'type' => 'varchar',
			'label' => 'Hero Title',
			'group' => 'Content',
			'visible' => true,
			'required' => false,
			'user_defined' => false,
			'default' => ''
		]);
	}

}