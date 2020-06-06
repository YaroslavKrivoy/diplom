<?php
namespace Webfitters\Checkout\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface {

	public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$setup->startSetup();
		if (version_compare($context->getVersion(), "1.0.1", "<")) {
	        $tables = [
				$setup->getTable('quote_item'),
				$setup->getTable('sales_order_item'),
				$setup->getTable('sales_shipment_item'),
				$setup->getTable('sales_invoice_item')
			];
			foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'case_qty', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					'nullable' => true,	
					'default' => null,
					'length' => '10,4',
					'comment' => 'Quantity of Cases'
				]);
			}
		}
		if(version_compare($context->getVersion(), '1.0.2', '<')) {
			$tables = [
				$setup->getTable('quote'),
				$setup->getTable('sales_order'),
				$setup->getTable('sales_shipment'),
				$setup->getTable('sales_invoice')
			];
			foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'uses_container_fee', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => true,	
					'default' => null,
					'comment' => 'Order uses container fee'
				]);
			}
		}
		if(version_compare($context->getVersion(), '1.0.3', '<')) {
			$tables = [
				$setup->getTable('quote'),
				$setup->getTable('sales_order'),
				$setup->getTable('sales_shipment'),
				$setup->getTable('sales_invoice')
			];
			foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'container_fee', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					'nullable' => true,	
					'default' => null,
					'length' => '10,4',
					'comment' => 'Applied container fee'
				]);
			}
		}
		if(version_compare($context->getVersion(), '1.0.4', '<')) {
			$tables = [
				$setup->getTable('quote'),
				$setup->getTable('sales_order'),
				$setup->getTable('sales_shipment'),
				$setup->getTable('sales_invoice')
			];
			foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'container_fee_additive', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => true,	
					'default' => null,
					'comment' => 'Container fee is additive'
				]);
			}
		}
		$setup->endSetup();
	}

}