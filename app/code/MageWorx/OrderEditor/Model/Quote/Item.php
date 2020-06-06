<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model\Quote;

use Magento\Catalog\Model\Product;

class Item extends \Magento\Quote\Model\Quote\Item
{
    /**
     * @var string
     */
    const PREFIX_ID = 'q';
}
