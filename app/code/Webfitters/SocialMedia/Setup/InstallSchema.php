<?php
namespace Webfitters\SocialMedia\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$table = 
		$setup->getConnection()->newTable($setup->getTable('wf_socialposts'))
		->addColumn('id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Post ID'
		)
		->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Image URL')
		->addColumn('author', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Author')
		->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''], 'Title')
		->addColumn('description', \Magento\Framework\Db\Ddl\Table::TYPE_TEXT, 2400, ['nullable' => true, 'default' => ''], 'Description')
		->addColumn('source',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => ''],'Review Source')
		->addColumn('rating', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, null, ['nullable' => true], 'Review Rating')
		->addColumn('date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Review Date')
		->setComment('Social media post table');
		$setup->getConnection()->createTable($table);
	}

}