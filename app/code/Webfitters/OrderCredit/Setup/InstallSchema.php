<?php
namespace Webfitters\OrderCredit\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$tables = [
			$setup->getTable('sales_order'),
			$setup->getTable('quote'),
			$setup->getTable('sales_invoice')
		];
		foreach($tables as $table){
			$setup->getConnection()->addColumn($table, 'base_credit_amount', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
				'nullable' => true,	
				'default' => null,
				'length' => '10,4',
				'comment' => 'Applied Credit'
			]);
			$setup->getConnection()->addColumn($table, 'credit_amount', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
				'nullable' => true,	
				'default' => null,
				'length' => '10,4',
				'comment' => 'Applied Credit'
			]);
		}
	}

}