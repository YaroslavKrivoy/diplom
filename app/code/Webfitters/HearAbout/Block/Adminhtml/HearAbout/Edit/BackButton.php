<?php
namespace Webfitters\HearAbout\Block\Adminhtml\HearAbout\Edit;

class BackButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface {

    public function getButtonData() {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    public function getBackUrl() {
        return $this->getUrl('*/*/');
    }
    
}