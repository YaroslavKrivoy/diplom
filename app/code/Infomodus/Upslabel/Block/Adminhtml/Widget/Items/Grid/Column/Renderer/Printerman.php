<?php

namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items\Grid\Column\Renderer;

class Printerman extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        if ($row->getLstatus() == 1) {
            return '';
        }

        $pdf = '';
        $hvr = false;
        $html = '';
        $image = '';
        $invoice = '';
        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/label/HVR/' . $row->getTrackingnumber() . ".html")) {
            $hvr = ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/label/HVR' . $row->getTrackingnumber() . '.html" target="_blank">HVR</a>';
        }

        if ($row->getTypePrint() == "GIF") {
            $pdf = '<a href="' . $this->getUrl('infomodus_upslabel/pdflabels/one', ['label_id' => $row->getId()]) . '" target="_blank">'.__('PDF').'</a>';
            $image = ' / <a href="' . $this->getUrl('infomodus_upslabel/items/printer', ['imname' => 'label' . $row->getTrackingnumber() . '.gif']) . '" target="_blank">' . __('Image') . '</a>';
        } elseif ($row->getTypePrint() == "link") {
            $pdf = '<a href="' . $row->getLabelname() . '" target="_blank">'.__('Print Return Label').'</a>';
        } elseif ($row->getTypePrint() !== "virtual") {
            if ($this->_config->getStoreConfig('upslabel/printing/automatic_printing') == 1) {
                $pdf = '<a href="' . $this->getUrl('infomodus_upslabel/items/autoprint', ['label_id' => $row->getId(), 'order_id' => $row->getOrderId(), 'type_print' => 'auto']) . '" target="_blank">' . __('Print thermal') . '</a>';
            } else {
                $printersText = $this->_config->getStoreConfig('upslabel/printing/printer_name');
                $printers = explode(",", $printersText);
                $pdf = '<a class="thermal-print-file" data-printer="' . (trim($printers[0])) . '" href="' . $this->getUrl('infomodus_upslabel/items/autoprint', ['label_id' => $row->getId(), 'order_id' => $row->getOrderId(), 'type_print' => 'manual']) . '">' . __('Print thermal') . '</a>';
            }
        }

        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/label/' . $row->getTrackingnumber() . '.html')) {
            $html .= ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/label/' . $row->getTrackingnumber() . '.html" target="_blank">' . __('Html') . '</a>';
        }

        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/turn_in_page/' . $row->getTrackingnumber() . '.html')) {
            $html .= ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/turn_in_page/' . $row->getTrackingnumber() . '.html" target="_blank">' . __('Turn-in copy') . '</a>';
        }

        if (file_exists($this->_config->getBaseDir('media') . '/upslabel/inter_pdf/' . $row->getShipmentidentificationnumber() . '.pdf')) {
            $invoice = ' / <a href="' . $this->_config->getBaseUrl('media') . 'upslabel/inter_pdf/' . $row->getShipmentidentificationnumber() . '.pdf" target="_blank">' . __('Invoice') . '</a>';
        }

        return $pdf . $html . $image . $hvr . $invoice;
    }
}
