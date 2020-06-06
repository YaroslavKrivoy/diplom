<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Block\Rma;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Api\Data\AddressInterface|null
     */
    protected $_address = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    protected $orderRepository;
    public $_order;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->currentCustomer = $currentCustomer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Prepare the layout of the address edit block.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('UPS RMA Create Label'));
        parent::_prepareLayout();


        if ($orderId = $this->getRequest()->getParam('order_id')) {
            try {
                $this->_order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $e) {
                $this->_order = null;
            }
        }
        return $this;
    }

    /**
     * Return the Url for saving.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'upslabel/rma/formPost',
            ['_secure' => true, 'order_id' => $this->getRequest()->getParam('order_id')]
        );
    }

    /**
     * Return back button Url, either to customer address or account.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->getRequest()->getParam('order_id')]);
    }

    /**
     * Get config value.
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
