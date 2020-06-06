<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Model\ResourceModel\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'upslabel_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Infomodus\Upslabel\Model\Items', 'Infomodus\Upslabel\Model\ResourceModel\Items');
    }
    public function addGroup($value)
    {
        $this->getSelect()->group($value);
        return $this;
    }
    public function getCreditmemoId()
    {
        $this->getShipmentId();
    }
}
