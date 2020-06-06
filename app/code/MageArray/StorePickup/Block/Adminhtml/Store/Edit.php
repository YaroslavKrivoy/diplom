<?php

namespace MageArray\StorePickup\Block\Adminhtml\Store;

/**
 * Class Edit
 * @package MageArray\StorePickup\Block\Adminhtml\Store
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'storepickup_id';
        $this->_blockGroup = 'MageArray_StorePickup';
        $this->_controller = 'adminhtml_store';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Store'));
        $this->buttonList->update('delete', 'label', __('Delete Store'));

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'],
                    ],
                ],
            ],
            10
        );
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }
}
