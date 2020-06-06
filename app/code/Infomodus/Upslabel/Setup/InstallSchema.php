<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabel'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'upslabel_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Label ID'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Title'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Order ID'
            )->addColumn(
                'shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Shipment ID'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => 'shipment'],
                'Type'
            )->addColumn(
                'type_print',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                11,
                ['nullable' => false, 'default' => 'GIF'],
                'Type print'
            )->addColumn(
                'trackingnumber',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Tracking number'
            )->addColumn(
                'shipmentidentificationnumber',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Shipment Identification number'
            )->addColumn(
                'shipmentdigest',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Shipment Identification number'
            )->addColumn(
                'labelname',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Label name'
            )->addColumn(
                'lstatus',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                ['nullable' => false, 'default' => '1'],
                'Status'
            )->addColumn(
                'statustext',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Status description'
            )->addColumn(
                'track_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Track status'
            )->addColumn(
                'track_status_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => '-1'],
                'Track status code'
            )->addColumn(
                'international_invoice',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                3,
                ['nullable' => false, 'default' => '0'],
                'International invoice'
            )->addColumn(
                'rva_printed',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Printed'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Update time'
            )->setComment(
                'UPS labels'
            );
            $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
