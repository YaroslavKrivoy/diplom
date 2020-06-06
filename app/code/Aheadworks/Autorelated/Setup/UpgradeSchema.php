<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aheadworks\Autorelated\Model\Config;
use Aheadworks\Autorelated\Setup\Updater\Schema\Updater;

/**
 * Upgrade the Autorelated module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param Updater $updater
     */
    public function __construct(
        Updater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<=')) {
            $this->addRelatedTable($setup);
            $this->migrateStoreCustomerGroupData($setup);
            $this->addForeignKeyToProfitTable($setup);
            $this->modifyRuleTable($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.4', '<=')) {
            $this->addIsDisplayOutOfStockFieldToRuleTable($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.0', '<=')) {
            if ($this->isNeedToAddCustomerSegmentTable($setup)) {
                $this->addCustomerSegmentTable($setup);
            }
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.3.1', '<=')) {
            $this->addPrimaryKeyToProfitTable($setup);
            $this->addProductConditionTypeFieldToRuleTable($setup);
            $this->addWbtabTables($setup);
            $this->addWvtavTables($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.5.0', '<=')) {
            $this->updater->update150($setup);
        }
    }

    /**
     * Add related table store and customer group for the rule table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addRelatedTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_autorelated_rule_store'
         */
        $storeTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_autorelated_rule_store')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_store', ['rule_id']),
            ['rule_id']
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('aw_autorelated_rule_store', 'rule_id', 'aw_autorelated_rule', 'id'),
            'rule_id',
            $setup->getTable('aw_autorelated_rule'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('aw_autorelated_rule_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Autorelated Rule To Store Relation Table'
        );
        $setup->getConnection()->createTable($storeTable);

        /**
         * Create table 'aw_autorelated_rule_customer_group'
         */
        $customerGroupTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_autorelated_rule_customer_group')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule ID'
        )->addColumn(
            'customer_group_id',
            $this->getCustomerGroupIdType($setup),
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group ID'
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_customer_group', ['rule_id']),
            ['rule_id']
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $setup->getFkName('aw_autorelated_rule_customer_group', 'rule_id', 'aw_autorelated_rule', 'id'),
            'rule_id',
            $setup->getTable('aw_autorelated_rule'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'aw_autorelated_rule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $setup->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Autorelated Rule To Customer Group Relation Table'
        );
        $setup->getConnection()->createTable($customerGroupTable);

        return $this;
    }

    /**
     * Migrate and delete store and customer group data from aw_autorelated_rule table into aw_autorelated_rule_store
     * and aw_autorelated_rule_customer_group tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function migrateStoreCustomerGroupData(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $select = $connection->select()
            ->from($setup->getTable('aw_autorelated_rule'), ['id', 'store_ids', 'customer_groups']);
        $ruleData = $connection->fetchAssoc($select);

        $toInsertStore = [];
        $toInsertCustomerGroup = [];
        foreach ($ruleData as $rule) {
            $storeIds = explode(',', $rule['store_ids']);
            $customerGroupIds = explode(',', $rule['customer_groups']);
            foreach ($storeIds as $storeId) {
                $toInsertStore[] = [
                    'rule_id' => (int)$rule['id'],
                    'store_id' => (int)$storeId,
                ];
            }
            foreach ($customerGroupIds as $customerGroupId) {
                $toInsertCustomerGroup[] = [
                    'rule_id' => (int)$rule['id'],
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
        }
        if (count($toInsertStore)) {
            $connection->insertMultiple($setup->getTable('aw_autorelated_rule_store'), $toInsertStore);
        }
        if (count($toInsertCustomerGroup)) {
            $connection->insertMultiple($setup->getTable('aw_autorelated_rule_customer_group'), $toInsertCustomerGroup);
        }

        $connection->dropColumn($setup->getTable('aw_autorelated_rule'), 'store_ids');
        $connection->dropColumn($setup->getTable('aw_autorelated_rule'), 'customer_groups');

        return $this;
    }

    /**
     * Add ForeignKey to aw_autorelated_profit table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addForeignKeyToProfitTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $temporaryTableName = 'aw_autorelated_profit_tmp';
        // Create temporary table from aw_autorelated_profit table
        $connection->createTemporaryTableLike($temporaryTableName, 'aw_autorelated_profit');

        // Migrate data from aw_autorelated_profit table to temporary table
        $select = $connection->select()
            ->from(['main_table' => $setup->getTable('aw_autorelated_profit')]);
        $connection->query(
            $connection->insertFromSelect($select, $setup->getTable($temporaryTableName))
        );

        // Drop table aw_autorelated_profit
        $connection->dropTable($setup->getTable('aw_autorelated_profit'));

        // Create new table aw_autorelated_profit
        $profitTable = $connection->newTable($setup->getTable('aw_autorelated_profit'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule ID'
            )
            ->addColumn(
                'view_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'View Count'
            )
            ->addColumn(
                'click_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Click Count'
            )
            ->addIndex(
                $setup->getIdxName($setup->getTable('aw_autorelated_profit'), ['rule_id']),
                ['rule_id'],
                ['type' => 'unique']
            )->addForeignKey(
                $setup->getFkName('aw_autorelated_profit', 'rule_id', 'aw_autorelated_rule', 'id'),
                'rule_id',
                $setup->getTable('aw_autorelated_rule'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Autorelated Rule To Profit Relation Table');
        $connection->createTable($profitTable);

        $select = $connection->select()
            ->from(
                ['main_table' => $setup->getTable('aw_autorelated_rule')],
                ['rule_id' => 'id']
            )->joinLeft(
                ['profit_tmp' => $setup->getTable($temporaryTableName)],
                'profit_tmp.rule_id = main_table.id',
                [
                    'view_count' => new \Zend_Db_Expr('IFNULL(view_count, 0)'),
                    'click_count' => new \Zend_Db_Expr('IFNULL(click_count, 0)')
                ]
            );

        // Migrate data from temporary table to aw_autorelated_profit
        $connection->query(
            $connection->insertFromSelect($select, $setup->getTable('aw_autorelated_profit'))
        );

        // Drop temporary table
        $connection->dropTemporaryTable($setup->getTable($temporaryTableName));

        return $this;
    }

    /**
     * Modify aw_autorelated_rule table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function modifyRuleTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable('aw_autorelated_rule'),
            'category_ids',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Category Ids',
                'after' => 'is_display_addtocart'
            ]
        );

        return $this;
    }

    /**
     * Modify aw_autorelated_rule table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addIsDisplayOutOfStockFieldToRuleTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable('aw_autorelated_rule'),
            'is_display_outofstock',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'comment' => 'Is Display Out of Stock Products',
                'after' => 'is_display_addtocart'
            ]
        );

        return $this;
    }

    /**
     * Check if enterprise customer segment table exist
     *
     * @param SchemaSetupInterface $setup
     * @return bool
     * @throws \Zend_Db_Exception
     */
    private function isNeedToAddCustomerSegmentTable(SchemaSetupInterface $setup)
    {
        return ($setup->tableExists($setup->getTable(Config::CUSTOMER_SEGMENT_TABLE_NAME)));
    }

    /**
     * Create aw_autorelated_rule_customer_segment table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addCustomerSegmentTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_autorelated_rule_customer_segment'
         */
        $customerSegmentTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_autorelated_rule_customer_segment')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule ID'
        )->addColumn(
            'customer_segment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Segment ID'
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_customer_segment', ['rule_id']),
            ['rule_id']
        )->addIndex(
            $setup->getIdxName('aw_autorelated_rule_customer_segment', ['customer_segment_id']),
            ['customer_segment_id']
        )->addForeignKey(
            $setup->getFkName('aw_autorelated_rule_customer_segment', 'rule_id', 'aw_autorelated_rule', 'id'),
            'rule_id',
            $setup->getTable('aw_autorelated_rule'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'aw_autorelated_rule_customer_segment',
                'customer_segment_id',
                Config::CUSTOMER_SEGMENT_TABLE_NAME,
                'segment_id'
            ),
            'customer_segment_id',
            $setup->getTable(Config::CUSTOMER_SEGMENT_TABLE_NAME),
            'segment_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Autorelated Rule To Customer Segment Relation Table'
        );
        $setup->getConnection()->createTable($customerSegmentTable);

        return $this;
    }

    /**
     * Retrieve type of 'customer_group_id' field in 'customer_group' table
     *
     * @param SchemaSetupInterface $setup
     * @return string
     */
    private function getCustomerGroupIdType(SchemaSetupInterface $setup)
    {
        $customerGroupTable = $setup->getConnection()->describeTable($setup->getTable('customer_group'));
        $customerGroupIdType = $customerGroupTable['customer_group_id']['DATA_TYPE'] == 'int'
            ? \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER : $customerGroupTable['customer_group_id']['DATA_TYPE'];
        return $customerGroupIdType;
    }

    /**
     * Add PrimaryKey to aw_autorelated_profit table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addPrimaryKeyToProfitTable($setup)
    {
        $connection = $setup->getConnection();
        $temporaryTableName = $setup->getTable('aw_autorelated_profit_tmp_131');
        // Create temporary table from aw_autorelated_profit table
        $connection->createTemporaryTableLike($temporaryTableName, $setup->getTable('aw_autorelated_profit'));

        // Migrate data from aw_autorelated_profit table to temporary table
        $select = $connection->select()
            ->from(['main_table' => $setup->getTable('aw_autorelated_profit')]);
        $connection->query(
            $connection->insertFromSelect($select, $setup->getTable($temporaryTableName))
        );

        // Drop table aw_autorelated_profit
        $connection->dropTable($setup->getTable('aw_autorelated_profit'));

        // Create new table aw_autorelated_profit
        $profitTable = $connection->newTable($setup->getTable('aw_autorelated_profit'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false,
                    'primary' => true
                ],
                'Rule ID'
            )
            ->addColumn(
                'view_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'View Count'
            )
            ->addColumn(
                'click_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Click Count'
            )
            ->addIndex(
                $setup->getIdxName($setup->getTable('aw_autorelated_profit'), ['rule_id']),
                ['rule_id'],
                ['type' => 'unique']
            )->addForeignKey(
                $setup->getFkName('aw_autorelated_profit', 'rule_id', 'aw_autorelated_rule', 'id'),
                'rule_id',
                $setup->getTable('aw_autorelated_rule'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Autorelated Rule To Profit Relation Table');
        $connection->createTable($profitTable);

        $select = $connection->select()
            ->from(
                ['main_table' => $setup->getTable('aw_autorelated_rule')],
                ['rule_id' => 'id']
            )->joinLeft(
                ['profit_tmp' => $setup->getTable($temporaryTableName)],
                'profit_tmp.rule_id = main_table.id',
                [
                    'view_count' => new \Zend_Db_Expr('IFNULL(view_count, 0)'),
                    'click_count' => new \Zend_Db_Expr('IFNULL(click_count, 0)')
                ]
            );

        // Migrate data from temporary table to aw_autorelated_profit
        $connection->query(
            $connection->insertFromSelect($select, $setup->getTable('aw_autorelated_profit'))
        );

        // Drop temporary table
        $connection->dropTemporaryTable($setup->getTable($temporaryTableName));

        return $this;
    }

    /**
     * Modify aw_autorelated_rule table by adding product_condition_type column
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addProductConditionTypeFieldToRuleTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable('aw_autorelated_rule'),
            'product_condition_type',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'comment' => 'Product Condition Type',
                'after' => 'product_condition',
                'default' => \Aheadworks\Autorelated\Model\Source\ProductConditionType::DEFAULT_TYPE
            ]
        );

        return $this;
    }

    /**
     * Adding WBTAB tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addWbtabTables(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        /**
         * Create table 'aw_autorelated_wbtab_product'
         */
        $table = $connection
            ->newTable($setup->getTable('aw_autorelated_wbtab_product'))
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'related_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Related Product Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Orders Count'
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product', ['product_id']),
                ['product_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product', ['related_product_id']),
                ['related_product_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product', ['store_id']),
                ['store_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product', ['orders_count']),
                ['orders_count']
            )->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_wbtab_product',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_wbtab_product',
                    'related_product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'related_product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_wbtab_product',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Autorelated Wbtab Product Index');
        $connection->createTable($table);

        /**
         * Create table 'aw_autorelated_wbtab_product_idx'
         */
        $table = $connection
            ->newTable($setup->getTable('aw_autorelated_wbtab_product_idx'))
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'related_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Related Product Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Orders Count'
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product_idx', ['product_id']),
                ['product_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product_idx', ['related_product_id']),
                ['related_product_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product_idx', ['store_id']),
                ['store_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_wbtab_product_idx', ['orders_count']),
                ['orders_count']
            )->setComment('AW Autorelated Wbtab Product Index Idx');
        $connection->createTable($table);

        return $this;
    }

    /**
     * Adding WVTAV tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addWvtavTables(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        /**
         * Create table 'aw_autorelated_wvtav_index'
         */
        $table = $connection
            ->newTable($setup->getTable('aw_autorelated_wvtav_index'))
            ->addColumn(
                'index_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Primary Index Id'
            )
            ->addColumn(
                'master_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0',],
                'Master Product Id'
            )
            ->addColumn(
                'slave_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0',],
                'Slave Product Id'
            )
            ->addColumn(
                'rating',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['unsigned' => true, 'nullable' => false, 'default' => '0',],
                'Rating of viewed product'
            )
            ->addColumn(
                'session_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Session Time'
            )
            ->addIndex(
                $setup->getIdxName('aw_autorelated_wvtav_index', ['master_product_id']),
                ['master_product_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_autorelated_wvtav_index', ['slave_product_id']),
                ['slave_product_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_wvtav_index',
                    'master_product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'master_product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_wvtav_index',
                    'slave_product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'slave_product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Autorelated WVTAV Index Table');
        $connection->createTable($table);

        /**
         * Create table 'aw_autorelated_rule_wvtav_params'
         */
        $wvtavParamsTable = $connection->newTable(
            $setup->getTable('aw_autorelated_rule_wvtav_params')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'primary' => true, 'unsigned' => true, 'nullable' => false],
            'Rule ID'
        )->addColumn(
            'suggest_only_one_category',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0',],
            'Suggest Products from One Category Only'
        )->addColumn(
            'suggest_only_price_higher',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0',],
            'Suggest Products Only with Price Higher than Price of Current Product'
        )->addIndex(
            $setup->getIdxName('aw_autorelated_profit', ['rule_id']),
            ['rule_id'],
            ['type' => 'unique']
        )->addForeignKey(
            $setup->getFkName(
                'aw_autorelated_rule_wvtav_params',
                'rule_id',
                'aw_autorelated_rule',
                'id'
            ),
            'rule_id',
            $setup->getTable('aw_autorelated_rule'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Autorelated WVTAV Product Condition Type Params Table'
        );
        $connection->createTable($wvtavParamsTable);

        return $this;
    }
}
