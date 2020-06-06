<?php
namespace Webfitters\HearAbout\Block\Adminhtml\HearAbout\Edit;

class DeleteButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface {

    public function getButtonData() {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __('Delete Source'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\''
                    . __('Are you sure you want to delete this source?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl() {
        return $this->getUrl('*/*/delete', ['pfay_contacts_id' => $this->getId()]);
    }

}