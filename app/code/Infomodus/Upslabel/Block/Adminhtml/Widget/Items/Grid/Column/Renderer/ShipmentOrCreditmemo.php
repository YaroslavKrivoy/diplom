<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items\Grid\Column\Renderer;

class ShipmentOrCreditmemo extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        if ($row->getShipmentId() > 0) {
            $path = 'sales/shipment/view/shipment_id/';
            if ($row->getType() == 'refund') {
                $path = 'sales/creditmemo/view/creditmemo_id/';
            }

            return '<a href="' . $this->getUrl($path . $row->getShipmentId()) . '">'
                . $row->getShipmentIncrementId() . '</a>';
        }

        return '';
    }
}
