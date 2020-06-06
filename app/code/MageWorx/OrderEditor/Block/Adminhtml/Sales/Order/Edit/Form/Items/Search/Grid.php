<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Search;

use MageWorx\OrderEditor\Model\Order;
use InvalidArgumentException;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var Order
     */
    protected $order;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param Order $order
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        Order $order,
        array $data = []
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->helperData = $helperData;
        $this->order = $order;
        parent::__construct(
            $context,
            $backendHelper,
            $productFactory,
            $catalogConfig,
            $sessionQuote,
            $salesConfig,
            $data
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'sales/order_create/loadBlock',
            ['block' => 'search_grid', '_current' => true, 'collapse' => null]
        );
    }

    /**
     * Get store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        if ($orderId !== null) {
            $this->order->load($orderId);
            return $this->order->getStore();
        }

        return $this->sessionQuote->getStore();
    }
}
