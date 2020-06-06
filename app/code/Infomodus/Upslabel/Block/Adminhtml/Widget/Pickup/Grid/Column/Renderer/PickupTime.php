<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Pickup\Grid\Column\Renderer;

class PickupTime extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $rowData = $row->getData();
        return $rowData['PickupDateDay'].'.'.$rowData['PickupDateMonth'].'.'.$rowData['PickupDateYear'].' &nbsp;&nbsp;&nbsp;'.str_replace(',', ':', $rowData['ReadyTime']).' - '.str_replace(',', ':', $rowData['CloseTime']);
    }
}
