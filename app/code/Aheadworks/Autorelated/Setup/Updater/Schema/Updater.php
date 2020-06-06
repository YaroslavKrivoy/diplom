<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Setup\Updater\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Updater
 *
 * @package Aheadworks\Autorelated\Setup\Updater\Schema
 */
class Updater
{
    /**
     * Update to 1.4.1 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function update150(SchemaSetupInterface $setup)
    {
        $this->addRuleBlockTitleTable($setup);
        return $this;
    }

    /**
     * Adding frontend block title table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addRuleBlockTitleTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_autorelated_rule_block_title'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Value'
            )->addIndex(
                $setup->getIdxName('aw_autorelated_rule_block_title', ['rule_id']),
                ['rule_id']
            )->addIndex(
                $setup->getIdxName('aw_autorelated_rule_block_title', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'aw_autorelated_rule_block_title',
                    'rule_id',
                    'aw_autorelated_rule',
                    'id'
                ),
                'rule_id',
                $setup->getTable('aw_autorelated_rule'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_autorelated_rule_block_title', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'AW Autorelated Rule Block Title table'
            );
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
