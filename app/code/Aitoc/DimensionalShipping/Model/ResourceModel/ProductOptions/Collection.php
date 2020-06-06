<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\DimensionalShipping\Model\ProductOptions',
            'Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions'
        );
    }
}
