<?php
namespace Webfitters\Pdf\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$table = 
		$setup->getConnection()->newTable($setup->getTable('wf_pdf'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Post ID'
		)
		->addColumn('file', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'File Path')
		->addColumn('link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'File Link')
		->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created Date')
		->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Updated Date')
		->setComment('Unique PDF Table');
		$setup->getConnection()->createTable($table);
	}

}