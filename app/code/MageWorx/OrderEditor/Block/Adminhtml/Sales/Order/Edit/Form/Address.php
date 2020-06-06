<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form;

use Magento\Backend\Block\Template;

class Address extends Template
{
    /**
     * @var string
     */
    private $addressType = '';

    /**
     * @param string $addressType
     * @return $this
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddressForm()
    {
        $addressForm = $this->getChildBlock(
            'mageworx_ordereditor_address_form'
        );

        $this->setAddressType($this->getRequest()->getParam('address_type'));

        if ($addressForm) {
            $formBlock = $addressForm->getChildBlock('form');
            if (empty($formBlock)) {
                return '';
            }

            $formBlock->setDisplayVatValidationButton(false);

            $form = $formBlock->getForm();

            if (!empty($this->addressType)) {
                $form->addFieldNameSuffix($this->addressType . '_address');
                $form->setHtmlNamePrefix($this->addressType . '_address');
                $form->setHtmlIdPrefix($this->addressType . '_address_');
                $form->setId($this->addressType . '_address_edit_form');
            }

            return $form->toHtml();
        }

        return '';
    }

    /**
     * Return "Submit" button html
     *
     * @return string
     */
    public function getSubmitButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'address-'.$this->addressType.'-submit',
                    'label' => __('Submit'),
                    'type' => 'button',
                    'class' => 'edit primary',
                    'style' => 'margin-top: 1em; float:right;',
                ]
            )
            ->toHtml();

        return $html;
    }

    /**
     * Return "Cancel" button html
     *
     * @return string
     */
    public function getCancelButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'address-'.$this->addressType.'-cancel',
                    'label' => __('Cancel'),
                    'type' => 'button',
                    'class' => 'edit primary',
                    'style' => 'margin-top: 1em; float:left;',
                ]
            )
            ->toHtml();

        return $html;
    }
}
