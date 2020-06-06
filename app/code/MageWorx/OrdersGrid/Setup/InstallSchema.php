<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrdersGrid\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\OrdersGrid\Helper\Data as Helper;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'mageworx_ordersgrid_grid'
         */
        $gridTable = $installer->getConnection()->newTable(
            $installer->getTable(Helper::TABLE_NAME_EXTENDED_GRID)
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Order ID'
        )->addColumn(
            'coupon_code',
            Table::TYPE_TEXT,
            255,
            [],
            'Coupon Code'
        )->addColumn( // Product
            'product_thumbnail',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Product Image'
        )->addColumn(
            'product_name',
            Table::TYPE_TEXT,
            '32k',
            [],
            'Product Name'
        )->addColumn(
            'product_sku',
            Table::TYPE_TEXT,
            '32k',
            [],
            'Product SKU'
        )->addColumn( // Invoices
            'invoices',
            Table::TYPE_TEXT,
            '16k',
            [],
            'Invoices'
        )->addColumn( // Shipments
            'shipments',
            Table::TYPE_TEXT,
            '16k',
            [],
            'Shipments'
        )->addColumn( // Taxes
            'applied_tax_code',
            Table::TYPE_TEXT,
            255,
            [],
            'Applied Tax Code'
        )->addColumn(
            'applied_tax_percent',
            Table::TYPE_INTEGER,
            '3',
            [],
            'Applied Tax Percent'
        )->addColumn(
            'applied_tax_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Applied Tax Amount'
        )->addColumn(
            'applied_tax_base_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Applied Tax Base Amount'
        )->addColumn(
            'applied_tax_base_real_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Applied Tax Base Real Amount'
        )->addColumn( // Shipment Tracking
            'tracking_number',
            Table::TYPE_TEXT,
            '16k',
            [],
            'Tracking Number'
        )->addColumn( // Billing Address
            'billing_fax',
            Table::TYPE_TEXT,
            64,
            [],
            'Fax (Billing)'
        )->addColumn(
            'billing_region',
            Table::TYPE_TEXT,
            255,
            [],
            'Region (Billing)'
        )->addColumn(
            'billing_postcode',
            Table::TYPE_TEXT,
            255,
            [],
            'Postcode (Billing)'
        )->addColumn(
            'billing_city',
            Table::TYPE_TEXT,
            255,
            [],
            'City (Billing)'
        )->addColumn(
            'billing_telephone',
            Table::TYPE_TEXT,
            255,
            [],
            'Telephone (Billing)'
        )->addColumn(
            'billing_country_id',
            Table::TYPE_TEXT,
            2,
            [],
            'Country Id (Billing)'
        )->addColumn( // Shipping Address
            'shipping_fax',
            Table::TYPE_TEXT,
            64,
            [],
            'Fax (Shipping)'
        )->addColumn(
            'shipping_region',
            Table::TYPE_TEXT,
            255,
            [],
            'Region (Shipping)'
        )->addColumn(
            'shipping_postcode',
            Table::TYPE_TEXT,
            255,
            [],
            'Postcode (Shipping)'
        )->addColumn(
            'shipping_city',
            Table::TYPE_TEXT,
            255,
            [],
            'City (Shipping)'
        )->addColumn(
            'shipping_telephone',
            Table::TYPE_TEXT,
            255,
            [],
            'Telephone (Shipping)'
        )->addColumn(
            'shipping_country_id',
            Table::TYPE_TEXT,
            2,
            [],
            'Country Id (Shipping)'
        )->addColumn(
            'subtotal_purchased',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subtotal (Purchased)'
        )->addColumn(
            'weight',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Weight'
        )->addColumn(
            'last_updated_time',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => true,
            ],
            'Last Updated Time'
        )->addForeignKey(
            $installer->getFkName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                'order_id',
                'sales_order_grid',
                'entity_id'
            ),
            'order_id',
            $installer->getTable('sales_order_grid'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['subtotal_purchased']
            ),
            ['subtotal_purchased']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['weight']
            ),
            ['weight']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['billing_region']
            ),
            ['billing_region']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['billing_postcode']
            ),
            ['billing_postcode']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['billing_city']
            ),
            ['billing_city']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['billing_telephone']
            ),
            ['billing_telephone']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['billing_country_id']
            ),
            ['billing_country_id']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['shipping_region']
            ),
            ['shipping_region']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['shipping_postcode']
            ),
            ['shipping_postcode']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['shipping_city']
            ),
            ['shipping_city']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['shipping_telephone']
            ),
            ['shipping_telephone']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['shipping_country_id']
            ),
            ['shipping_country_id']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['applied_tax_code']
            ),
            ['applied_tax_code']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['applied_tax_percent']
            ),
            ['applied_tax_percent']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['applied_tax_base_amount']
            ),
            ['applied_tax_base_amount']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                ['applied_tax_base_real_amount']
            ),
            ['applied_tax_base_real_amount']
        )->addIndex(
            $installer->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                [
                    'coupon_code',
                    'product_name',
                    'product_sku',
                    'invoices',
                    'shipments',
                    'tracking_number',
                    'billing_fax',
                    'shipping_fax',
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [
                'coupon_code',
                'product_name',
                'product_sku',
                'invoices',
                'shipments',
                'tracking_number',
                'billing_fax',
                'shipping_fax',
            ],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment('MageWorx Extended Orders Grid table');

        $installer->getConnection()->createTable($gridTable);
        $installer->endSetup();
    }
}
