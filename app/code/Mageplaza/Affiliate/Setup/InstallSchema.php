<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	/**
	 * install tables
	 *
	 * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
	 * @param \Magento\Framework\Setup\ModuleContextInterface $context
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		$connection = $installer->getConnection();

		/** Table mageplaza_affiliate_account */
		if ($installer->tableExists('mageplaza_affiliate_account')) {
			$connection->dropTable($installer->getTable('mageplaza_affiliate_account'));
		}
		$table = $connection->newTable($installer->getTable('mageplaza_affiliate_account'))
			->addColumn(
				'account_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Account ID'
			)->addColumn(
				'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Account Customer ID'
			)->addColumn(
				'code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Account Affiliate Code'
			)->addColumn(
				'group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Account Affiliate Group'
			)->addColumn(
				'balance', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false', 'default' => 0.00], 'Account Balance'
			)->addColumn(
				'holding_balance', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false', 'default' => 0.00], 'Account Holding Balance'
			)->addColumn(
				'total_commission', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false', 'default' => 0.00], 'Account Total Commission'
			)->addColumn(
				'total_paid', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false', 'default' => 0.00], 'Account Total Paid'
			)->addColumn(
				'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Account Status'
			)->addColumn(
				'email_notification', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [], 'Account Email Notification'
			)->addColumn(
				'parent', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Account Referred By'
			)->addColumn(
				'tree', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Account Tier Path'
			)->addColumn(
				'withdraw_method', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Withdraw method'
			)->addColumn(
				'withdraw_information', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Withdraw information'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Account Created At'
			)->setComment('Account Table');
		$connection->createTable($table);

		/** Table mageplaza_affiliate_group */
		if ($installer->tableExists('mageplaza_affiliate_group')) {
			$connection->dropTable($installer->getTable('mageplaza_affiliate_group'));
		}
		$table = $connection->newTable($installer->getTable('mageplaza_affiliate_group'))
			->addColumn(
				'group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Group ID'
			)->addColumn(
				'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Group Name'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Group Created At'
			)->setComment('Group Table');
		$connection->createTable($table);

		/** Table mageplaza_affiliate_campaign */
		if ($installer->tableExists('mageplaza_affiliate_campaign')) {
			$connection->dropTable($installer->getTable('mageplaza_affiliate_campaign'));
		}
		$table = $connection->newTable($installer->getTable('mageplaza_affiliate_campaign'))
			->addColumn(
				'campaign_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
				'Campaign ID'
			)->addColumn(
				'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Campaign Name'
			)->addColumn(
				'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Campaign Description'
			)->addColumn(
				'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Campaign Status'
			)->addColumn(
				'website_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable => false'], 'Campaign Website IDs'
			)->addColumn(
				'affiliate_group_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable => false'], 'Campaign Affiliate Groups'
			)->addColumn(
				'from_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [], 'Campaign Active From Date'
			)->addColumn(
				'to_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [], 'Campaign Active To Date'
			)->addColumn(
				'display', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Campaign Display'
			)->addColumn(
				'sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Campaign Sort Order'
			)->addColumn(
				'conditions_serialized', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Campaign Conditions Serialized'
			)->addColumn(
				'actions_serialized', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Campaign Actions Serialized'
			)->addColumn(
				'commission', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Campaign Commission'
			)->addColumn(
				'discount_action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Campaign Discount Action'
			)->addColumn(
				'discount_amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['default' => '0'], 'Campaign Discount Amount'
			)->addColumn(
				'discount_qty', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false', 'default' => '0'], 'Campaign Discount Qty'
			)->addColumn(
				'discount_step', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => '0'], 'Campaign Discount Step'
			)->addColumn(
				'discount_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable => false'], 'Campaign Discount Description'
			)->addColumn(
				'free_shipping', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [], 'Campaign Free Shipping'
			)->addColumn(
				'apply_to_shipping', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [], 'Campaign Apply To Shipping'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Campaign Created At'
			)->addColumn(
				'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Campaign Updated At'
			)->setComment('Campaign Table');
		$connection->createTable($table);

		/** Table mageplaza_affiliate_transaction */
		if ($installer->tableExists('mageplaza_affiliate_transaction')) {
			$connection->dropTable($installer->getTable('mageplaza_affiliate_transaction'));
		}
		$table = $connection->newTable($installer->getTable('mageplaza_affiliate_transaction'))
			->addColumn(
				'transaction_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Transaction ID'
			)->addColumn(
				'account_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Affiliate Account'
			)->addColumn(
				'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Customer ID'
			)->addColumn(
				'action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Action'
			)->addColumn(
				'type', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Action Type'
			)->addColumn(
				'amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', [], 'Amount'
			)->addColumn(
				'amount_used', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', [], 'Amount Used'
			)->addColumn(
				'current_balance', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', [], 'Current Balance'
			)->addColumn(
				'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Status'
			)->addColumn(
				'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable => false'], 'Title'
			)->addColumn(
				'order_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Order ID'
			)->addColumn(
				'order_increment_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Order ID'
			)->addColumn(
				'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Store ID'
			)->addColumn(
				'campaign_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Campaign'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Created At'
			)->addColumn(
				'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Updated At'
			)->addColumn(
				'holding_to', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [], 'Holding Time'
			)->addColumn(
				'extra_content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Content'
			)->setComment('Transaction Table');
		$connection->createTable($table);

		/** Table mageplaza_affiliate_withdraw */
		if ($installer->tableExists('mageplaza_affiliate_withdraw')) {
			$connection->dropTable($installer->getTable('mageplaza_affiliate_withdraw'));
		}
		$table = $connection->newTable($installer->getTable('mageplaza_affiliate_withdraw'))
			->addColumn(
				'withdraw_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Withdraw ID'
			)->addColumn(
				'account_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Withdraw Account'
			)->addColumn(
				'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Customer'
			)->addColumn(
				'transaction_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Transaction ID'
			)->addColumn(
				'amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false'], 'Withdraw Amount'
			)->addColumn(
				'fee', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false'], 'Withdraw Fee'
			)->addColumn(
				'transfer_amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', ['nullable => false'], 'Withdraw Transaction Amount'
			)->addColumn(
				'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Status'
			)->addColumn(
				'payment_method', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable => false'], 'Withdraw Payment Method'
			)->addColumn(
				'payment_details', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Withdraw Payment Details'
			)->addColumn(
				'resolved_at', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Withdraw Resolved At'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Withdraw Created At'
			)->setComment('Withdraw Table');
		$connection->createTable($table);

		//Add discount field in order table
		$affiliate = [
			'affiliate_key'                  => ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'comment' => 'Affiliate Key'],
			'referral_email' => ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'comment' => 'Referral Email'],
			'affiliate_commission'           => ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'comment' => 'Affiliate Commission'],
			'affiliate_discount_amount'      => ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'nullable' => true, 'default' => '0', 'comment' => 'Affiliate Discount Amount',],
			'base_affiliate_discount_amount' => ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'nullable' => true, 'default' => '0', 'comment' => 'Base Affiliate Discount Amount',],
		];

		//add to sales order
		$connection->addColumn('sales_order', 'affiliate_key', $affiliate['affiliate_key']);
		$connection->addColumn('sales_order', 'referral_email', $affiliate['referral_email']);
		$connection->addColumn('sales_order_grid', 'referral_email', $affiliate['referral_email']);
		$connection->addColumn('sales_order', 'affiliate_commission', $affiliate['affiliate_commission']);
		$connection->addColumn('sales_order', 'affiliate_discount_amount', $affiliate['affiliate_discount_amount']);
		$connection->addColumn('sales_order', 'base_affiliate_discount_amount', $affiliate['base_affiliate_discount_amount']);

		// add to sales invoice
		$connection->addColumn('sales_invoice', 'affiliate_discount_amount', $affiliate['affiliate_discount_amount']);
		$connection->addColumn('sales_invoice', 'base_affiliate_discount_amount', $affiliate['base_affiliate_discount_amount']);

		// add to sales credit memo
		$connection->addColumn('sales_creditmemo', 'affiliate_discount_amount', $affiliate['affiliate_discount_amount']);
		$connection->addColumn('sales_creditmemo', 'base_affiliate_discount_amount', $affiliate['base_affiliate_discount_amount']);

		$installer->endSetup();
	}
}
