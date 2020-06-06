<?php
namespace Webfitters\HearAbout\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context){
		$tables = [
			$setup->getTable('sales_order'),
			$setup->getTable('quote')
		];
		foreach($tables as $table){
			$setup->getConnection()->addColumn($table, 'hear_about_id', [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				'nullable' => true,	
				'default' => null,
				'comment' => 'Hear About ID'
			]);
		}

		$table = $setup->getConnection()->newTable($setup->getTable('wf_hear_abouts'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Type ID'
		)
		->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => true, 'default' => null, 'length' => 255], 'Hear About Name')
		->setComment('Hear about table');
		$setup->getConnection()->createTable($table);
	}

}