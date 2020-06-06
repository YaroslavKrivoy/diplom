<?php
namespace Webfitters\HearAbout\Block\Adminhtml\HearAbout\Edit;

class ResetButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface {

    public function getButtonData() {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }

}