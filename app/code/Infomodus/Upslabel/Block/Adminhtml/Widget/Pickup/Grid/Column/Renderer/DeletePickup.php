<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Pickup\Grid\Column\Renderer;

class DeletePickup extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
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
        return '<a href="' . $this->getUrl('infomodus_upslabel/pickup/delete', ['id' => $row->getId()]) . '">' . __('Delete') . '</a>';
    }
}
