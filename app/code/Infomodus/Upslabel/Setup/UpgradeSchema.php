<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Infomodus\Upslabel\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $this->createAccountTable($setup);
        }
        if (version_compare($context->getVersion(), '0.1.2', '<')) {
            $this->addColumnType2($setup);
            $this->addColumnXmllog($setup);
        }
        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $this->addColumnPrice($setup);
        }
        if (version_compare($context->getVersion(), '0.1.4', '<')) {
            $this->addColumnOrderAndSCIncrement($setup);
        }
        if (version_compare($context->getVersion(), '0.1.5', '<')) {
            $this->addColumnShipmentidentificationnumber2($setup);
        }
        if (version_compare($context->getVersion(), '0.1.6', '<')) {
            $this->createPickupTable($setup);
        }
        if (version_compare($context->getVersion(), '0.1.7', '<')) {
            $this->createConformityTable($setup);
        }
        if (version_compare($context->getVersion(), '0.1.8', '<')) {
            $this->addColumnLabelStoreId($setup);
        }
        if (version_compare($context->getVersion(), '8.5.5', '<')) {
            $this->changeLabelName($setup);
        }
        if (version_compare($context->getVersion(), '8.6.1', '<')) {
            $this->createBoxesTable($setup);
        }
        if (version_compare($context->getVersion(), '8.6.2', '<')) {
            $this->changeFieldLengthName($setup);
        }
        if (version_compare($context->getVersion(), '8.6.3', '<')) {
            $this->createAddressTable($setup);
        }
        if (version_compare($context->getVersion(), '8.6.4', '<')) {
            $this->addAddressResAndPickup($setup);
        }

        $setup->endSetup();
    }

    public function addAddressResAndPickup(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel_address');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'residential',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'residential address indicator'
                ]
            );

            $setup->getConnection()->addColumn(
                $tableName,
                'pickup_point',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'residential address indicator'
                ]
            );
        }
    }

    public function createAddressTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel_address');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabel_address'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'address_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Box ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'name'
            )->addColumn(
                'company',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'company'
            )->addColumn(
                'attention',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'attention'
            )->addColumn(
                'phone',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'phone'
            )->addColumn(
                'street_one',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'street_one'
            )->addColumn(
                'street_two',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'street_two'
            )->addColumn(
                'room',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'room'
            )->addColumn(
                'floor',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'floor'
            )->addColumn(
                'city',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'city'
            )->addColumn(
                'province_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'province_code'
            )->addColumn(
                'urbanization',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'urbanization'
            )->addColumn(
                'postal_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'postal_code'
            )->addColumn(
                'country',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'country'
            )->setComment(
                'UPS Addresses'
            );
            $setup->getConnection()->createTable($table);
        }
    }

    public function changeFieldLengthName(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel_box');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->changeColumn(
                $tableName,
                'length',
                'lengths',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '7,2',
                ]
            );

            $setup->getConnection()->changeColumn(
                $tableName,
                'outer_length',
                'outer_lengths',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '7,2',
                ]
            );
        }
    }

    public function createBoxesTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel_box');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabel_box'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'box_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Box ID'
            )->addColumn(
                'enable',
                Table::TYPE_INTEGER,
                2,
                ['nullable' => false, 'default' => 1],
                'enable'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'name'
            )->addColumn(
                'width',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'width'
            )->addColumn(
                'outer_width',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'outer_width'
            )->addColumn(
                'height',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'height'
            )->addColumn(
                'outer_height',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'outer_height'
            )->addColumn(
                'length',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'length'
            )->addColumn(
                'outer_length',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'outer_length'
            )->addColumn(
                'max_weight',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'max_weight'
            )->addColumn(
                'empty_weight',
                Table::TYPE_DECIMAL,
                '7,2',
                ['nullable' => false, 'default' => 0],
                'empty_weight'
            )->setComment(
                'UPS Boxes'
            );
            $setup->getConnection()->createTable($table);
        }
    }

    public function changeLabelName(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->changeColumn(
                $tableName,
                'labelname',
                'labelname',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                ]
            );
        }
    }

    public function addColumnLabelStoreId(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'store_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Store Id'
                ]
            );
        }
    }
    public function addColumnShipmentidentificationnumber2(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'shipmentidentificationnumber_2',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Shipment Identification number origin'
                ]
            );
        }
    }
    public function addColumnOrderAndSCIncrement(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'order_increment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Order Increment Id'
                ]
            );
            $setup->getConnection()->addColumn(
                $tableName,
                'shipment_increment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Shipment or Creditmemo Increment Id'
                ]
            );
        }
    }
    public function addColumnPrice(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'price',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '7,2',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Price'
                ]
            );
            $setup->getConnection()->addColumn(
                $tableName,
                'currency',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 3,
                    'nullable' => false,
                    'default' => 'USD',
                    'comment' => 'Currency'
                ]
            );
        }
    }
    public function addColumnType2(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'type_2',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => false,
                    'default' => 'shipment',
                    'comment' => 'Type 2'
                ]
            );
        }
    }
    public function addColumnXmllog(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabel');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'xmllog',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Xml log'
                ]
            );
        }
    }

    public function createConformityTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabelconformity');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabelaccount'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'conformity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Conformity ID'
            )->addColumn(
                'method_id',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'method id'
            )->addColumn(
                'upsmethod_id',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'upsmethod id'
            )->addColumn(
                'country_ids',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'country ids'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => 1],
                'store id'
            )->setComment(
                'UPS Conformity'
            );
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function createAccountTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabelaccount');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabelaccount'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'account_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Account ID'
            )->addColumn(
                'companyname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Company name'
            )->addColumn(
                'attentionname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Attention name'
            )->addColumn(
                'address1',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Address 1'
            )->addColumn(
                'address2',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Address 2'
            )->addColumn(
                'address3',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Address 3'
            )->addColumn(
                'country',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Country'
            )->addColumn(
                'postalcode',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Postal code'
            )->addColumn(
                'city',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'City'
            )->addColumn(
                'province',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Province'
            )->addColumn(
                'telephone',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Telephone'
            )->addColumn(
                'fax',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Fax'
            )->addColumn(
                'accountnumber',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ''],
                'Account number'
            )->addColumn(
                'created_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Update time'
            )->setComment(
                'UPS Account'
            );
            $setup->getConnection()->createTable($table);
        }
    }

    public function createPickupTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('upslabelpickup');
        if ($setup->getConnection()->isTableExists($tableName) == false) {
            /**
             * Create table 'upslabelaccount'
             */
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'pickup_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pickup ID'
            )->addColumn(
                'RatePickupIndicator',
                Table::TYPE_TEXT,
                1,
                ['nullable' => false, 'default' => 'N'],
                'Rate Pickup Indicator'
            )->addColumn(
                'CloseTime',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'Close Time'
            )->addColumn(
                'ReadyTime',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'Ready Time'
            )->addColumn(
                'PickupDateYear',
                Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => ''],
                'Pickup Date Year'
            )->addColumn(
                'PickupDateMonth',
                Table::TYPE_TEXT,
                2,
                ['nullable' => false, 'default' => ''],
                'Pickup Date Month'
            )->addColumn(
                'PickupDateDay',
                Table::TYPE_TEXT,
                2,
                ['nullable' => false, 'default' => ''],
                'Pickup Date Day'
            )->addColumn(
                'AlternateAddressIndicator',
                Table::TYPE_TEXT,
                1,
                ['nullable' => false, 'default' => 'N'],
                'Alternate Address Indicator'
            )->addColumn(
                'ServiceCode',
                Table::TYPE_TEXT,
                5,
                ['nullable' => false, 'default' => ''],
                'Service Code'
            )->addColumn(
                'Quantity',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Quantity'
            )->addColumn(
                'DestinationCountryCode',
                Table::TYPE_TEXT,
                2,
                ['nullable' => false, 'default' => ''],
                'Destination Country Code'
            )->addColumn(
                'ContainerCode',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'Container Code'
            )->addColumn(
                'Weight',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default' => ''],
                'Weight'
            )->addColumn(
                'UnitOfMeasurement',
                Table::TYPE_TEXT,
                5,
                ['nullable' => false, 'default' => ''],
                'Unit Of Measurement'
            )->addColumn(
                'OverweightIndicator',
                Table::TYPE_TEXT,
                1,
                ['nullable' => false, 'default' => 'N'],
                'Overweight Indicator'
            )->addColumn(
                'PaymentMethod',
                Table::TYPE_TEXT,
                5,
                ['nullable' => false, 'default' => ''],
                'Payment Method'
            )->addColumn(
                'SpecialInstruction',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Special Instruction'
            )->addColumn(
                'ReferenceNumber',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Reference Number'
            )->addColumn(
                'Notification',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'default' => 0],
                'Notification'
            )->addColumn(
                'ConfirmationEmailAddress',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Confirmation Email Address'
            )->addColumn(
                'UndeliverableEmailAddress',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Undeliverable Email Address'
            )->addColumn(
                'ShipFrom',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Ship From'
            )->addColumn(
                'pickup_request',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'pickup request'
            )->addColumn(
                'pickup_response',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'pickup response'
            )->addColumn(
                'pickup_cancel',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'pickup cancel'
            )->addColumn(
                'pickup_cancel_request',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'pickup cancel_request'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'status'
            )->addColumn(
                'price',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => '0'],
                'price'
            )->addColumn(
                'store',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => 1],
                'store'
            )->addColumn(
                'created_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Update time'
            )->setComment(
                'UPS Pickup'
            );
            $setup->getConnection()->createTable($table);
        }
    }
}
