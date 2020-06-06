<?php

namespace Aitoc\DimensionalShipping\Block\Adminhtml\Order\View;

class BoxItems extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    /**
     * Show edit url for administrators
     */
    const SHOW_EDIT_URL = 1;

    /**
     * Adding our block's html data to order's information
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTemplate('Magento_Sales::order/view/info.phtml');
        $block = $this->getLayout()->createBlock(
            'Aitoc\DimensionalShipping\Block\Adminhtml\Sales\Order\Invoice\BoxItems',
            'adminhtml_sales_order_view_boxitems'
        );
        $block->setEditUrl(self::SHOW_EDIT_URL);

        return parent::_toHtml() . $block->_toHtml();
    }
}
