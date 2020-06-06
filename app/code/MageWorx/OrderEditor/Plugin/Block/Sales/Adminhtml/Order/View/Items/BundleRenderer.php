<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View\Items;

use Magento\Backend\Block\Template;

class BundleRenderer
{
    /**
     * @param Template $originalBlock
     */
    public function beforeToHtml(Template $originalBlock)
    {
        $originalBlock->setTemplate('MageWorx_OrderEditor::order/view/items/renderer/bundle.phtml');
    }
}
