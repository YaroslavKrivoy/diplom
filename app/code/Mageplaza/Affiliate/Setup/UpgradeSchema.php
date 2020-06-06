<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$connection = $installer->getConnection();
			if ($installer->tableExists('mageplaza_affiliate_banner')) {
				$connection->dropTable($installer->getTable('mageplaza_affiliate_banner'));
			}
			$table = $connection->newTable($installer->getTable('mageplaza_affiliate_banner'))
				->addColumn(
					'banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
					['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
					'Banner ID'
				)->addColumn(
					'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Banner Title'
				)->addColumn(
					'content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Banner Content'
				)->addColumn(
					'link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Banner Link'
				)->addColumn(
					'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Banner Status'
				)->addColumn(
					'rel_nofollow', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 255, [], 'Banner Rel Nofollow'
				)->addColumn(
					'campaign_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Banner Campaign ID'
				)->addColumn(
					'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Banner Created At'
				)->addColumn(
					'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Banner Updated At'
				)->setComment('Banner Table');

			$connection->createTable($table);
		}
	}
}