<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Account\Widget;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Chooser
 * @package MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Account\Widget
 *
 * @method Chooser setUseAjax($bool)
 */
class Chooser extends Extended
{
    const CHOOSER_URL = 'ordereditor/edit_account_widget/chooser';

    /**
     * @var array
     */
    protected $selectedCustomers = [];

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $resourceCustomer;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer $resourceCustomer
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer $resourceCustomer,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resourceCustomer = $resourceCustomer;
        $this->storeFactory = $storeFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqueId = $this->mathRandom->getUniqueHash($element->getId());

        $sourceUrl = $this->getUrl(
            static::CHOOSER_URL,
            ['unique_id' => $uniqueId, 'use_massaction' => false]
        );

        /** @var \Magento\Widget\Block\Adminhtml\Widget\Chooser $chooser */
        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqueId
        );

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $customerId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'customer') {
                $customerId = $value[1];
            }
            $label = '';
            if ($customerId) {
                $label .= $this->resourceCustomer->getAttributeRawValue(
                    $customerId,
                    'name',
                    $this->_storeManager->getStore()
                );
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    window.selectedCustomer(trElement);
                }
            ';
    }

    /**
     * Disable mass select
     *
     * @return bool
     */
    public function getUseMassaction()
    {
        return false;
    }

    /**
     * Prepare customers collection, defined collection filters
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Magento\Customer\Model\ResourceModel\Customer\Collection */
        $collection = $this->collectionFactory->create()->addAttributeToSelect('*');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for customers grid
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'chooser_email',
            [
                'header' => __('Email'),
                'name' => 'chooser_email',
                'index' => 'email',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku',
            ]
        );
        $this->addColumn(
            'chooser_firstname',
            [
                'header' => __('Firstname'),
                'name' => 'chooser_firstname',
                'index' => 'firstname',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );
        $this->addColumn(
            'chooser_lastname',
            [
                'header' => __('Lastname'),
                'name' => 'chooser_lastname',
                'index' => 'lastname',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );
        $this->addColumn(
            'chooser_group_id',
            [
                'header' => __('Group'),
                'name' => 'chooser_group_id',
                'index' => 'group_id',
                'type' => 'options',
                'options' => $this->groupCollectionFactory->create()->toOptionHash(),
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );
        $this->addColumn(
            'chooser_store_id',
            [
                'header' => __('Store'),
                'name' => 'chooser_store_id',
                'index' => 'store_id',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'chooser_store_id',
                [
                    'header' => __('Store'),
                    'sortable' => false,
                    'index' => 'store_id',
                    'type' => 'options',
                    'options' => $this->storeFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }

        $this->addColumn(
            'chooser_created_at',
            [
                'header' => __('Created At'),
                'name' => 'chooser_created_at',
                'type' => 'datetime',
                'align' => 'center',
                'index' => 'created_at',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );
        $this->addColumn(
            'chooser_updated_at',
            [
                'header' => __('Updated At'),
                'name' => 'chooser_updated_at',
                'type' => 'datetime',
                'align' => 'center',
                'index' => 'updated_at',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            static::CHOOSER_URL,
            [
                'customers_grid' => true,
                '_current' => true,
                'unique_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction(),
            ]
        );
    }

    /**
     * Setter
     *
     * @param array $selectedCustomers
     * @return $this
     */
    public function setSelectedCustomers($selectedCustomers)
    {
        $this->selectedCustomers = $selectedCustomers;

        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedCustomers()
    {
        if ($selectedCustomers = $this->getRequest()->getParam('selected_customers', null)) {
            $this->setSelectedCustomers($selectedCustomers);
        }

        return $this->selectedCustomers;
    }
}
