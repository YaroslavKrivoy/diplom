<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Model\ResourceModel\Boxes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Infomodus\Upslabel\Model\Boxes', 'Infomodus\Upslabel\Model\ResourceModel\Boxes');
    }
}
