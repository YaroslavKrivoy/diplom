<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('infomodus_items_form');
        $this->setTitle(__('Shipping Information'));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $params = ['type' => $this->getRequest()->getParam('direction'),
            'order_id' => $this->getRequest()->getParam('order_id')];
        if ($this->getRequest()->getParam('shipment_id', null) !== null) {
            $params['shipment_id'] = $this->getRequest()->getParam('shipment_id', null);
        }

        if ($this->getRequest()->getParam('redirect_path', null) !== null) {
            $params['redirect_path'] = $this->getRequest()->getParam('redirect_path', null);
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('infomodus_upslabel/items/save', $params),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
