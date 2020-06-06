<?php
namespace Webfitters\Shipping\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface {

	public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$setup->startSetup();
		if (version_compare($context->getVersion(), "1.0.1", "<")) {
			$tables = [
				$setup->getTable('quote'),
				$setup->getTable('sales_order'),
				$setup->getTable('sales_shipment'),
				$setup->getTable('sales_invoice')
			];
			foreach($tables as $table){
				$setup->getConnection()->addColumn($table, 'time_in_transit', [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => true,	
					'default' => null,
					'comment' => 'Estimated Time In Transit'
				]);
			}
		}
		$setup->endSetup();
	}

}