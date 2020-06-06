<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View\Items;

use Magento\Backend\Block\Template;

class DefaultRenderer
{
    /**
     * @param Template $originalBlock
     * @param array $after
     * @return array
     */
    public function afterGetColumns(Template $originalBlock, $after)
    {
        $after = ['thumbnail' => "col-thumbnail"] + $after;
        return $after;
    }
}
