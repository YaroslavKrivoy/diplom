<?php

namespace MageArray\StorePickup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magearray_pickup_store')
        )->addColumn(
            'storepickup_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Store Pickup Id'
        )->addColumn(
            'store_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Store Name'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Sort Order'
        )->addColumn(
            'phone_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Phone Number'
        )->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Address'
        )->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'City'
        )->addColumn(
            'zipcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Zipcode'
        )->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Country'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'State'
        )->addColumn(
            'latitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Latitude'
        )->addColumn(
            'longitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Longitude'
        )->addColumn(
            'opening_days',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Opening Days'
        )->addColumn(
            'working_hours',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Working Hours'
        );
        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'pickup_date',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Pickup Date',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'pickup_date',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Pickup Date',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'pickup_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Pickup Store',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'pickup_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Pickup Store',
            ]
        );

        $setup->endSetup();
    }
}
