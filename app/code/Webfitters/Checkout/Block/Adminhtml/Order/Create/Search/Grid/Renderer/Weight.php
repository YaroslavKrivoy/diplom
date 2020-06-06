<?php
namespace Webfitters\Checkout\Block\Adminhtml\Order\Create\Search\Grid\Renderer;

class Weight extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input {

    protected $typeConfig;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->typeConfig = $typeConfig;
    }

    protected function _isInactive($row) {
        return $this->typeConfig->isProductSet($row->getTypeId());
    }

    public function render(\Magento\Framework\DataObject $row) {
        $disabled = '';
        $addClass = '';
        if ($this->_isInactive($row)) {
            $qty = '';
            $disabled = 'disabled="disabled" ';
            $addClass = ' input-inactive';
        } else {
            $qty = $row->getData($this->getColumn()->getIndex());
            $qty *= 1;
            if (!$qty) {
                $qty = '';
            }
        }
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getName() . '" ';
        $html .= 'value="' . $qty . '" ' . $disabled;
        $html .= 'class="input-text admin__control-text ' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
