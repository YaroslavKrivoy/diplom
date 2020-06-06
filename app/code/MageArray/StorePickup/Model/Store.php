<?php

namespace MageArray\StorePickup\Model;

use Magento\Framework\Model\AbstractModel;

class Store extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('MageArray\StorePickup\Model\ResourceModel\Store');
    }
}
