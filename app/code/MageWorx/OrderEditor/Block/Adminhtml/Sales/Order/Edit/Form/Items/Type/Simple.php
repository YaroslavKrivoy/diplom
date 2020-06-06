<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Type;

class Simple extends AbstractType
{
    /**
     * @return bool
     */
    public function hasStockQty()
    {
        return true;
    }
}
