<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Block of links in Order view page
 */
namespace Infomodus\Upslabel\Block\Order\Info;

class Labels extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    public $_conf;
    protected $_labels;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Infomodus\Upslabel\Helper\Config $config,
        \Infomodus\Upslabel\Model\ResourceModel\Items\Collection $labels,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_conf = $config;
        $this->_labels = $labels;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    public function getLabels()
    {
        if ($this->_conf->getStoreConfig('upslabel/return/frontend_customer_return')==0) {
            return null;
        }
        return $this->_labels
            ->addFieldToFilter('order_id', $this->getOrder()->getId())
            ->addFieldToFilter('type', 'refund')->addFieldToFilter('lstatus', '0');
    }
}
