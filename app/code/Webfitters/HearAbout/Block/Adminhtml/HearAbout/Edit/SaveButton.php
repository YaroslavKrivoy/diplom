<?php
namespace Webfitters\HearAbout\Block\Adminhtml\HearAbout\Edit;

class SaveButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface {

    public function getButtonData() {
        return [
            'label' => __('Save Source'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'save']
                ]
            ],
            'sort_order' => 90,
        ];
    }

}