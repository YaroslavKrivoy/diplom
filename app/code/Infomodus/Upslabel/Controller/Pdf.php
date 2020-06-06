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
abstract class Pdf extends \Magento\Framework\App\Action\Action
{
    protected $handy;
    protected $pdf;
    protected $fileFactory;
    private $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Infomodus\Upslabel\Helper\Handy $handy,
        \Infomodus\Upslabel\Helper\Pdf $pdf,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->handy = $handy;
        $this->pdf = $pdf;
        $this->fileFactory = $fileFactory;
        $this->_customerSession = $customerSession;
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

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    /*public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
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
