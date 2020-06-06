<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Infomodus\Upslabel\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Customer address controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Rma extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $_formKeyValidator;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    /*protected $_formFactory;*/

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    /*protected $_dataProcessor;*/

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    /*protected $dataObjectHelper;*/

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    /*protected $resultForwardFactory;*/

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    private $_handy;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Api\Data\RegionInterfaceFactory $regionDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    )
    {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        /*$this->_formFactory = $formFactory;*/
        /*$this->_dataProcessor = $dataProcessor;*/
        /*$this->dataObjectHelper = $dataObjectHelper;*/
        /*$this->resultForwardFactory = $resultForwardFactory;*/
        $this->resultPageFactory = $resultPageFactory;
        $this->_handy = $handy;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    protected function _getFormKeyValidator()
    {
        return $this->_formKeyValidator;
    }
    protected function getHandy()
    {
        return $this->_handy;
    }

    protected function getResultPageFactory()
    {
        return $this->resultPageFactory;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    /*public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->getActionFlag()->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }*/

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = [])
    {
        /** @var \Magento\Framework\UrlInterface $urlBuilder */
        $urlBuilder = $this->_objectManager->create('Magento\Framework\UrlInterface');
        return $urlBuilder->getUrl($route, $params);
    }
}
