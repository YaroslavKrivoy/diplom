<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magento\Sales\Block\Adminhtml\Order\Create\Data
{
    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \MageWorx\OrderEditor\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $currencyFactory, $localeCurrency);
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->helperData->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->helperData->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->helperData->getStoreId();
    }

    /**
     * Get apply button html
     *
     * @return string
     */
    public function getSubmitButtonHtml()
    {
        $addButtonData = [
            'id' => 'order-items-submit',
            'label' => __('Submit'),
            'class' => 'edit primary',
            'style' => 'float:right',
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Get cancel button html
     *
     * @return string
     */
    public function getCancelButtonHtml()
    {
        $addButtonData = [
            'id' => 'order-items-cancel',
            'label' => __('Cancel'),
            'class' => 'edit primary',
            'style' => 'float:left',
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Return "Submit" button html
     *
     * @return string
     */
    public function getAddProductsButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'order-items-add-products',
                    'label' => __('Add Products'),
                    'type' => 'button',
                    'class' => 'edit action-secondary',
                    'style' => 'margin-right: 1em; float:right;',
                ]
            )
            ->toHtml();

        return $html;
    }
}
