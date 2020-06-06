<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';

    public function addGroupByNameField($field)
    {
        $this->getSelect()->group('main_table.' . $field);

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
            'Aitoc\DimensionalShipping\Model\OrderBox',
            'Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox'
        );
    }
}
