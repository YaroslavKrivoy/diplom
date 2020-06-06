<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Block\Adminhtml\System\Config\Buttons;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Sync extends Field
{
    /**
     * @var string
     */
    protected $_template = 'MageWorx_OrdersGrid::system/config/buttons/sync.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for the sync button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mageworx_ordersgrid/order_grid/sync');
    }

    /**
     * Generate the sync button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $data = [
            'id' => 'sync_button',
            'label' => __('Manually Synchronize Data'),
        ];

        /** @var \Magento\Backend\Block\Widget\Button $button */
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($data);
        $button->setDataAttribute(
            [
                'mage-init' =>
                    '{"MageWorx_OrdersGrid/js/system/config/buttons/sync": {
                            "submitUrl":"' . $this->getAjaxUrl() . '"
                        }
                    }',
            ]
        );

        return $button->toHtml();
    }
}
