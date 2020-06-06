<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Mageplaza\Affiliate\Helper\Data;

class Account
{
	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * @var Data
	 */
	protected $helper;

	/**
	 * @var array
	 */
	private $allowedActions = [];

	protected $_urlFactory;

	protected $response;
	protected $resultForwardFactory;

	/**
	 * @param Session $customerSession
	 * @param array $allowedActions List of actions that are allowed for not authorized users
	 */
	public function __construct(
		Session $customerSession,
		Data $helper,
		\Magento\Framework\UrlFactory $urlFactory,
		\Magento\Framework\App\Response\Http $response,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		array $allowedActions = []
	)
	{
		$this->session              = $customerSession;
		$this->allowedActions       = $allowedActions;
		$this->helper               = $helper;
		$this->_urlFactory          = $urlFactory;
		$this->response             = $response;
		$this->resultForwardFactory = $resultForwardFactory;
	}

	/**
	 * Dispatch actions allowed for not authorized users
	 *
	 * @param ActionInterface $subject
	 * @param \Closure $proceed
	 * @param RequestInterface $request
	 * @return mixed
	 */
	public function aroundDispatch(
		ActionInterface $subject,
		\Closure $proceed,
		RequestInterface $request
	)
	{
		if (!$this->helper->isEnable()) {
			$resultForward = $this->resultForwardFactory->create();
			$subject->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);

			return $resultForward->forward('noroute');
		}

		$action           = strtolower($request->getActionName());
		$patternAffiliate = '/^(' . implode('|', $this->allowedActions) . ')$/i';

		if (!$this->session->authenticate()) {
			$subject->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);
		} elseif (!preg_match($patternAffiliate, $action)) {
			if (!$this->affiliateAuthenticate()) {
				$subject->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);
			}
		} else {
			$this->session->setNoReferer(true);
		}

		$result = $proceed($request);
		$this->session->unsNoReferer(false);

		return $result;
	}

	public function affiliateAuthenticate()
	{
		$account = $this->helper->getCurrentAffiliate();
		if ($account && $account->getId()) {
			if ($account->isActive()) {
				return true;
			}

			$this->response->setRedirect($this->_createUrl()->getUrl('affiliate/', ['_current' => true]));
		} else {
			$this->session->setBeforeAuthUrl($this->_createUrl()->getUrl('*/*/*', ['_current' => true]));

			$this->response->setRedirect($this->_createUrl()->getUrl('affiliate/account/signup', ['_current' => true]));
		}

		return false;
	}

	protected function _createUrl()
	{
		return $this->_urlFactory->create();
	}
}
