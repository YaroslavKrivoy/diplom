<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'item_id';

    /**
     * @param $field
     *
     * @return $this
     */
    public function addGroupByNameField($field)
    {
        $this->getSelect()->group('main_table.' . $field);

        return $this;
    }

    /**
     * @param $condition
     * @param $columnData
     * @param $limit
     *
     * @return $this
     */
    public function setTableRecords($condition, $columnData, $limit)
    {
        $this->getConnection()->update(
            $this->getTable('aitoc_dimensional_shipping_order_item_boxes'),
            $columnData,
            $where = $condition
        );
        $this->getSelect()->limit($limit);
        return $this;
    }

    public function setLimit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\DimensionalShipping\Model\OrderItemBox',
            'Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox'
        );
    }
}
