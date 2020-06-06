<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */


namespace Aitoc\DimensionalShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductOptions extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('aitoc_dimensional_shipping_product_attributes', 'item_id');
    }
}
