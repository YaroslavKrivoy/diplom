<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use MageWorx\OrdersGrid\Helper\Data as Helper;
use Magento\Sales\Api\Data\OrderAddressInterface as OrderAddress;

/**
 * Order grid extended collection
 */
class Collection extends OriginalCollection
{
    const ALL_COLUMNS = [
        'coupon_code', // sales_order
        'weight', // sales_order
        'subtotal_purchased', // sales_order
        'product_name', // sales_order_item
        'product_sku', // sales_order_item
        'invoices', // sales_invoice
        'tracking_number', // sales_shipment_track
        'billing_fax',
        'billing_region',
        'billing_postcode',
        'billing_city',
        'billing_telephone',
        'billing_country_id',
        'shipping_fax',
        'shipping_region',
        'shipping_postcode',
        'shipping_city',
        'shipping_telephone',
        'shipping_country_id',
        'product_thumbnail'
    ];

    const ADDITIONAL_COLUMNS = [
        'coupon_code',
        'weight',
        'subtotal_purchased'
    ];

    const INVOICE_COLUMNS = [
        'invoices'
    ];

    const SHIPMENT_COLUMNS = [
        'shipments'
    ];

    const SHIPMENT_TRACKING_COLUMNS = [
        'tracking_number'
    ];

    const ORDER_ITEM_COLUMNS = [
        'product_name',
        'product_sku',
        'product_thumbnail'
    ];

    const BILLING_ADDRESS_COLUMNS = [
        'billing_fax',
        'billing_region',
        'billing_postcode',
        'billing_city',
        'billing_telephone',
        'billing_country_id',
    ];

    const SHIPPING_ADDRESS_COLUMNS = [
        'shipping_fax',
        'shipping_region',
        'shipping_postcode',
        'shipping_city',
        'shipping_telephone',
        'shipping_country_id',
    ];

    const TAX_COLUMNS = [
        'applied_tax_code',
        'applied_tax_percent',
        'applied_tax_amount',
        'applied_tax_base_amount',
        'applied_tax_base_real_amount',
    ];

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID);
        $columns = array_merge(
            static::ADDITIONAL_COLUMNS,
            static::INVOICE_COLUMNS,
            static::SHIPMENT_TRACKING_COLUMNS,
            static::SHIPMENT_COLUMNS,
            static::ORDER_ITEM_COLUMNS,
            static::BILLING_ADDRESS_COLUMNS,
            static::SHIPPING_ADDRESS_COLUMNS,
            static::TAX_COLUMNS
        );
        $this->getSelect()->joinLeft(
            ['eog' => $joinTable],
            'main_table.entity_id = eog.order_id',
            $columns
        );
        parent::_renderFiltersBefore();
    }

    /**
     * @param $orderId
     * @return bool
     */
    public function isOrderExists($orderId)
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($this->getTable(Helper::TABLE_NAME_EXTENDED_GRID), ['COUNT(`order_id`)']);
        $select->where('`order_id` = ' . $orderId);
        $result = $connection->fetchOne($select);

        return (bool)$result;
    }

    /**
     * Aggregate orders additional data in our table
     *
     * @param array $orderIds
     * @return $this
     */
    public function syncOrdersData($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        if (empty($orderIds)) {
            $connection->truncateTable($this->getTable(Helper::TABLE_NAME_EXTENDED_GRID));
        }

        $this->grabDataFromSalesOrderTable($orderIds);
        $this->grabDataFromInvoiceTable($orderIds);
        $this->grabDataFromTrackShipmentTable($orderIds);
        $this->grabDataFromShipmentTable($orderIds);
        $this->grabDataFromOrderItemsTable($orderIds);
        $this->grabDataFromOrderBillingAddressTable($orderIds);
        $this->grabDataFromOrderShippingAddressTable($orderIds);
        $this->grabDataFromSalesOrderTaxTable($orderIds);

        return $this;
    }

    /**
     * Grab data from the sales_order table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromSalesOrderTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select();
        $columns = [
            'order_id' => 'entity_id',
            'coupon_code' => 'coupon_code',
            'weight' => 'weight',
            'subtotal_purchased' => 'subtotal',
        ];
        $select->from($this->getTable('sales_order'), $columns);
        if (!empty($orderIds)) {
            $select->where('entity_id IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::ADDITIONAL_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_invoice table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromInvoiceTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $sourceTable = $this->getTable('sales_invoice');
        $select = $connection->select();
        // SELECT `order_id`, GROUP_CONCAT(DISTINCT `increment_id` SEPARATOR ', ') AS `invoices`
        // FROM `sales_invoice` GROUP BY `order_id`
        $columns = [
            'order_id' => 'order_id',
            'invoices' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `increment_id` SEPARATOR \',\')')
        ];
        $select->from($sourceTable, $columns)
            ->group('order_id');
        if (!empty($orderIds)) {
            $select->where('order_id IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::INVOICE_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_shipment table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromShipmentTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $sourceTable = $this->getTable('sales_shipment');
        $select = $connection->select();
        $columns = [
            'order_id' => 'order_id',
            'shipments' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `increment_id` SEPARATOR \',\')')
        ];
        $select->from($sourceTable, $columns)
            ->group('order_id');
        if (!empty($orderIds)) {
            $select->where('order_id IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::SHIPMENT_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_shipment_track table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromTrackShipmentTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $sourceTable = $this->getTable('sales_shipment_track');
        $select = $connection->select();
        $columns = [
            'order_id' => 'order_id',
            'tracking_number' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `track_number` SEPARATOR \',\')')
        ];
        $select->from($sourceTable, $columns)
            ->group('order_id');
        if (!empty($orderIds)) {
            $select->where('order_id IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::SHIPMENT_TRACKING_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_order_item table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromOrderItemsTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $sourceTable = $this->getTable('sales_order_item');
        $select = $connection->select();
        $columns = [
            'order_id' => 'order_id',
            'product_name' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `name` SEPARATOR \',\')'),
            'product_sku' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `sku` SEPARATOR \',\')'),
            'product_thumbnail' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT `product_id` SEPARATOR \',\')')
        ];
        $select->from($sourceTable, $columns)
            ->where('parent_item_id IS NULL')
            ->group('order_id');
        if (!empty($orderIds)) {
            $select->where('order_id IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::ORDER_ITEM_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_order table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromOrderBillingAddressTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select();
        $columns = [
            'order_id' => OrderAddress::PARENT_ID,
            'billing_fax' => OrderAddress::FAX,
            'billing_region' => OrderAddress::REGION,
            'billing_postcode' => OrderAddress::POSTCODE,
            'billing_city' => OrderAddress::CITY,
            'billing_telephone' => OrderAddress::TELEPHONE,
            'billing_country_id' => OrderAddress::COUNTRY_ID,
        ];
        $select->from($this->getTable('sales_order_address'), $columns)
            ->where('`address_type` = "billing"');
        if (!empty($orderIds)) {
            $select->where(OrderAddress::PARENT_ID . ' IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::BILLING_ADDRESS_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_order table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromOrderShippingAddressTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select();
        $columns = [
            'order_id' => OrderAddress::PARENT_ID,
            'shipping_fax' => OrderAddress::FAX,
            'shipping_region' => OrderAddress::REGION,
            'shipping_postcode' => OrderAddress::POSTCODE,
            'shipping_city' => OrderAddress::CITY,
            'shipping_telephone' => OrderAddress::TELEPHONE,
            'shipping_country_id' => OrderAddress::COUNTRY_ID,
        ];
        $select->from($this->getTable('sales_order_address'), $columns)
            ->where('`address_type` = "shipping"');
        if (!empty($orderIds)) {
            $select->where(OrderAddress::PARENT_ID . ' IN (' . implode(',', $orderIds) . ')');
        }

        $columnsToInsert = array_merge(['order_id'], static::SHIPPING_ADDRESS_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Grab data from the sales_order_tax table
     *
     * @param array $orderIds
     * @return $this
     */
    public function grabDataFromSalesOrderTaxTable($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select();
        $columns = [
            'order_id' => 'order_id',
            'applied_tax_code' => 'code',
            'applied_tax_percent' => 'percent',
            'applied_tax_amount' => 'amount',
            'applied_tax_base_amount' => 'base_amount',
            'applied_tax_base_real_amount' => 'base_real_amount',
        ];
        $select->from($this->getTable('sales_order_tax'), $columns);
        if (!empty($orderIds)) {
            $select->where('`order_id` IN (' . implode(',', $orderIds) . ')');
        }
        $select->where('`order_id` IN (SELECT `entity_id` FROM ' . $this->getTable('sales_order_grid') . ')');

        $columnsToInsert = array_merge(['order_id'], static::TAX_COLUMNS);
        $query = $connection->insertFromSelect(
            $select,
            $this->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $columnsToInsert,
            AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Delete data from table
     *
     * @param array $orderIds
     * @return $this
     */
    public function deleteData($orderIds = [])
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        if (empty($orderIds)) {
            $connection->truncateTable($this->getTable(Helper::TABLE_NAME_EXTENDED_GRID));

            return $this;
        }

        $select = $connection->select();
        $select->from($this->getTable(Helper::TABLE_NAME_EXTENDED_GRID));
        if (!empty($orderIds)) {
            if (is_string($orderIds)) {
                $orderIds = [$orderIds];
            }
            $select->where('`order_id` IN (' . implode(',', $orderIds) . ')');
        }
        $query = $select->deleteFromSelect($this->getTable(Helper::TABLE_NAME_EXTENDED_GRID));
        $connection->query($query);

        return $this;
    }
}
