<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */


namespace Aitoc\DimensionalShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderItemBox extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('aitoc_dimensional_shipping_order_item_boxes', 'item_id');
    }
}
