<?php
namespace Webfitters\Checkout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$table = $setup->getTable('sales_shipment_track');
		$setup->getConnection()->addColumn($table, 'shipping_charge', [
			'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
			'nullable' => true,	
			'default' => null,
			'length' => '10,4',
			'comment' => 'Shipping Charge'
		]);
		$tables = [
			$setup->getTable('quote_item'),
			$setup->getTable('sales_order_item'),
			$setup->getTable('sales_shipment_item'),
			$setup->getTable('sales_invoice_item')
		];
		foreach($tables as $table){
			$setup->getConnection()->addColumn($table, 'qty_weight', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
				'nullable' => true,	
				'default' => null,
				'length' => '10,4',
				'comment' => 'Quantity in Weight'
			]);
		}
		$table = $setup->getTable('catalog_product_entity_tier_price');
		$setup->getConnection()->addColumn($table, 'value_lb', [
			'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
			'nullable' => true,	
			'default' => null,
			'length' => '10,4',
			'comment' => 'Price per Pound'
		]);
	}

}