<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items\Grid\Column\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $this->update();
        return $this;
    }

    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getLstatus() == 1) {
            return '';
        }
        $HVR = false;
        $Html = '';
        $Image = '';
        $Invoice = '';
        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/label/HVR/' . $row->getTrackingnumber() . ".html")) {
            $HVR = ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/label/HVR' . $row->getTrackingnumber() . '.html" target="_blank">HVR</a>';
        }

        if ($row->getTypePrint() == "GIF") {
            $Pdf = '<a href="' . $this->getUrl('adminhtml/upslabel_pdflabels/onepdf/label_id/' . $row->getId()) . '" target="_blank">PDF</a>';
            $Image = ' / <a href="' . $this->getUrl('adminhtml/upslabel_upslabel/print/imname/' . 'label' . $row->getTrackingnumber() . '.gif') . '" target="_blank">Image</a>';
        } else {
            $Pdf = '<a href="' . $this->getUrl('adminhtml/upslabel_upslabel/autoprint/label_id/'.$row->getId()) . '" target="_blank">' . __('Print Label') . '</a>';
        }

        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/label/' . $row->getTrackingnumber() . '.html')) {
            $Html = ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/label/' . $row->getTrackingnumber() . '.html" target="_blank">Html</a>';
        }

        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/inter_pdf/' . $row->getShipmentidentificationnumber() . '.pdf')) {
            $Invoice = ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/inter_pdf/' . $row->getShipmentidentificationnumber() . '.pdf" target="_blank">Invoice</a>';
        }

        return $Pdf .  $Html . $Image . $HVR.$Invoice;
    }
}
