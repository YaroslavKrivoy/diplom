<?php
namespace Webfitters\Wholesale\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$table = 
		$setup->getConnection()->newTable($setup->getTable('wf_wholesale_types'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Type ID'
		)
		->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Image Path')
		->addColumn('hero', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Hero Path')
		->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Title')
		->addColumn('content', \Magento\Framework\Db\Ddl\Table::TYPE_TEXT, 5000, ['nullable' => true, 'default' => ''], 'Content')
		->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Sort Order')
		->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created Date')
		->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Updated Date')
		->setComment('Wholesale Type Table');
		$setup->getConnection()->createTable($table);
		$table = 
		$setup->getConnection()->newTable($setup->getTable('wf_wholesale_categories'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Category ID'
		)
		->addColumn('type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Type ID')
		->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Category Title')
		->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Sort Order')
		->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created Date')
		->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Updated Date')
		->setComment('Wholesale Category Table');
		$setup->getConnection()->createTable($table);
		$table = 
		$setup->getConnection()->newTable($setup->getTable('wf_wholesale_products'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Product ID'
		)
		->addColumn('category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Type ID')
		->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Item Description')
		->addColumn('item_number', \Magento\Framework\Db\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Item Number')
		->addColumn('size', \Magento\Framework\Db\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Item Pack/Size')
		->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Sort Order')
		->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created Date')
		->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Updated Date')
		->setComment('Wholesale Type Table');
		$setup->getConnection()->createTable($table);
	}

}