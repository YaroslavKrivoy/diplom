<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items\Grid\Column\Renderer;

class AddNewLabelLinks extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $shipmentId = null;
        if ($row->getShipmentId() > 0) {
            $shipmentId = $row->getShipmentId();
        }
        return $row->getTrackingnumber().
        '<br> <a href="'.$this->getUrl('infomodus_upslabel/items/edit', ['order_id' => $row->getOrderId(), 'direction' => 'shipment', 'shipment_id' => $shipmentId]).'">'.__('Add new UPS Shipping Label').'</a>
        <br> <a href="'.$this->getUrl('infomodus_upslabel/items/edit', ['order_id' => $row->getOrderId(), 'direction' => 'refund', 'shipment_id' => $shipmentId]).'">'.__('Add new UPS Return Label').'</a>
        <br> <a href="'.$this->getUrl('infomodus_upslabel/items/edit', ['order_id' => $row->getOrderId(), 'direction' => 'invert', 'shipment_id' => $shipmentId]).'">'.__('Add new UPS Invert Label').'</a>
        ';
    }
}
