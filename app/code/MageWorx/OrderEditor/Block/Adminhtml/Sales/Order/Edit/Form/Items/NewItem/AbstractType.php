<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\NewItem;

use \MageWorx\OrderEditor\Model\Quote\Item;
use \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Type\AbstractType as ItemsAbstract;

class AbstractType extends ItemsAbstract
{
    /**
     * @return string
     */
    public function getPrefixId()
    {
        return Item::PREFIX_ID;
    }

    /**
     * @return string
     */
    public function getEditedItemType()
    {
        return 'quote';
    }
}
