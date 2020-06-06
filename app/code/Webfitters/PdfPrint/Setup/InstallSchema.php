<?php
namespace Webfitters\PdfPrint\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$table = $setup->getTable('sales_order');
		$setup->getConnection()->addColumn($table, 'invoice_printed', [
			'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			'nullable' => true,	
			'default' => null,
			'length' => '1',
			'comment' => 'Invoice for order printed'
		]);
		$table = $setup->getTable('sales_order_grid');
		$setup->getConnection()->addColumn($table, 'invoice_printed', [
			'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			'nullable' => true,	
			'default' => null,
			'length' => '1',
			'comment' => 'Invoice for order printed'
		]);
	}

}