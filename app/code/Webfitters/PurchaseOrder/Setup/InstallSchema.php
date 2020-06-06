<?php
namespace Webfitters\PurchaseOrder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$tables = [
			$setup->getTable('quote'),
			$setup->getTable('sales_order'),
			$setup->getTable('sales_invoice'),
			$setup->getTable('sales_shipment')
		];
		foreach($tables as $table){
			$setup->getConnection()->addColumn($table, 'po_number', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => true,	
				'default' => null,
				'length' => '200',
				'comment' => 'Purchase Order Number'
			]);
		}
	}

}