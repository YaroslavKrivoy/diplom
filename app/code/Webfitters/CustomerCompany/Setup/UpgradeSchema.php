<?php
namespace Webfitters\CustomerCompany\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface {

	public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
	        $table = $setup->getTable('customer_grid_flat');
			$setup->getConnection()->addColumn($table, 'company', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => true,	
				'default' => null,
				'comment' => 'Customer Company'
			]);
        }
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
        	$tables = [
        		$setup->getTable('sales_invoice'),
        		$setup->getTable('sales_invoice_grid'),
        		$setup->getTable('sales_shipment'),
        		$setup->getTable('sales_shipment_grid')
        	];
        	foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'customer_id', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => true,	
					'default' => null,
					'comment' => 'Customer ID'
				]);
			}
        }
		$setup->endSetup();
	}

}