<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Pickup\Grid\Column\Renderer;

class Title extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $data = $row->getData();
        $xml = simplexml_load_string($data['pickup_response']);
        $soap = $xml->children('soapenv', true)->Body[0];
        $PRN = $soap->children('pkup', true)->PickupCreationResponse[0]->PRN;
        return __('PRN:')." ".$PRN
        ." ".__('Reference Number:')." ".$data['ReferenceNumber'];
    }
}
