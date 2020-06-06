<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml\Pickup;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_pickup';
        $this->_blockGroup = 'Infomodus_Upslabel';

        parent::_construct();

        $register = $this->_coreRegistry->registry('current_infomodus_upslabel_pickup');
        if ($register && $register->getId() > 0) {
            $this->buttonList->remove('save');
            if ($register->getStatus() != "Canceled") {
                $this->buttonList->add(
                    'cancel',
                    [
                        'label' => __('Cancel pickup'),
                        'onclick' => 'setLocation("' . $this->getUrl('infomodus_upslabel/pickup/cancel', ['id' => $register->getId()]) . '")',
                        'class' => 'save',
                    ],
                    9
                );
            }
        }

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => $register && $register->getId() > 0?__('Modify And Continue Edit'):__('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $item = $this->_coreRegistry->registry('current_infomodus_upslabel_pickup');
        if ($item->getId()) {
            return __("Edit Pickup '%1'", $this->escapeHtml($item->getName()));
        } else {
            return __('New Pickup');
        }
    }
}
