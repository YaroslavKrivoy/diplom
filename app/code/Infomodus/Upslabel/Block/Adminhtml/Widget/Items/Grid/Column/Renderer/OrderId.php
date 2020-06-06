<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items\Grid\Column\Renderer;

class OrderId extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public $_config;
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Infomodus\Upslabel\Helper\Config $config,
        array $data = []
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    public function setColumn($column)
    {
        parent::setColumn($column);
        return $this;
    }

    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return '<a href="' . $this->getUrl('sales/order/view/order_id/' . $row->getOrderId()) . '">' . $row->getOrderIncrementId() . '</a>';
    }
}
