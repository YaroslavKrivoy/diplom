<?php

namespace MageArray\StorePickup\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Store
 * @package MageArray\StorePickup\Model\ResourceModel
 */
class Store extends AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magearray_pickup_store', 'storepickup_id');
    }
}
