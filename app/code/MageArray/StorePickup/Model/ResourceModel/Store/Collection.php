<?php

namespace MageArray\StorePickup\Model\ResourceModel\Store;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package MageArray\StorePickup\Model\ResourceModel\Store
 */
class Collection extends AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'MageArray\StorePickup\Model\Store',
            'MageArray\StorePickup\Model\ResourceModel\Store'
        );
    }
}
