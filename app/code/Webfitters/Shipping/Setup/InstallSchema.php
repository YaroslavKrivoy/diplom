<?php
namespace Webfitters\Shipping\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$tables = [
			$setup->getTable('quote'),
			$setup->getTable('sales_order'),
			$setup->getTable('sales_shipment'),
			$setup->getTable('sales_invoice')
		];
		foreach($tables as $table){
			$setup->getConnection()->addColumn($table, 'additional_weight', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
				'nullable' => true,	
				'default' => null,
				'comment' => 'Addtl Shipping Weight',
				'size' => '10,2'
			]);
			$setup->getConnection()->addColumn($table, 'ups_data', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => true,	
				'default' => null,
				'comment' => 'UPS Data',
				'size' => '2M'
			]);
		}
	}

}