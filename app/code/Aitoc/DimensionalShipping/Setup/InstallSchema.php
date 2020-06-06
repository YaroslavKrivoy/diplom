<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */


namespace Aitoc\DimensionalShipping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->newTable(
            $setup->getTable('aitoc_dimensional_shipping_boxes')
        )->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Item Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Box title'
        )->addColumn(
            'weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Weight Box'
        )->addColumn(
            'empty_weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Empty Weight Box'
        )->addColumn(
            'one_day_weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Extra weight for one day shipping'
        )->addColumn(
            'two_day_weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Extra weight for two day shipping'
        )->addColumn(
            'three_day_weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Extra weight for three day shipping'
        )->addColumn(
            'four_day_weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Extra weight for four day shipping'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Width Box'
        )->addColumn(
            'height',
            \Magento\Framework\Db\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Height Box'
        )->addColumn(
            'length',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Length Box'
        )->addColumn(
            'outer_width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Outer width Box'
        )->addColumn(
            'outer_height',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Outer height Box'
        )->addColumn(
            'outer_length',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Outer length Box'
        )->addColumn(
            'unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Selected unit for box'
        )->setComment(
            'Aitoc Dimensional Shipping box settings table'
        );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('aitoc_dimensional_shipping_product_attributes')
        )->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Item Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Product id'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Width Product'
        )->addColumn(
            'height',
            \Magento\Framework\Db\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Height Product'
        )->addColumn(
            'length',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Length product'
        )->addColumn(
            'special_box',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Product for specific box'
        )->addColumn(
            'select_box',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Specific box id'
        )->addColumn(
            'pack_separately',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Pack product separately in  another box'
        )->addColumn(
            'unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Selected unit for product'
        )->setComment(
            'Aitoc Dimensional Shipping product attributes value'
        );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('aitoc_dimensional_shipping_order_item_boxes')
        )->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Item Id'
        )->addColumn(
            'order_box_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Order Box Id'
        )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                [],
                'Order Id'
            )->addColumn(
                'order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                [],
                'Order Item Id'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Order Item sku'
            )->addColumn(
                'separate',
                \Magento\Framework\Db\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'Item separate'
            )->addColumn(
                'not_packed',
                \Magento\Framework\Db\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'Not packed item.'
            )->addColumn(
                'error_message',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Information about error packing.'
            )->setComment(
                'Aitoc Dimensional Shipping order item boxes'
            );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('aitoc_dimensional_shipping_order_boxes')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Order Id'
        )->addColumn(
            'box_id',
            \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Box id'
        )->addColumn(
            'weight',
            \Magento\Framework\Db\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Weight box with items'
        )->setComment(
            'Aitoc Dimensional Shipping order boxes'
        );
        $setup->getConnection()->createTable($table);


        $setup->endSetup();
    }
}
