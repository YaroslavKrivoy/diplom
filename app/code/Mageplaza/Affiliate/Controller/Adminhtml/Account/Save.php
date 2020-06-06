<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

/**
 * Class Save
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Save extends \Mageplaza\Affiliate\Controller\Adminhtml\Account
{
	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * Constructor
	 *
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory
	)
	{
		$this->_customerFactory = $customerFactory;
		parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
	}

	/**
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data = $this->getRequest()->getPost('account')) {
			$account = $this->_initAccount();

			$account->addData($data);

			$this->_eventManager->dispatch(
				'affiliate_account_prepare_save', ['account' => $account, 'action' => $this]
			);

			$customer          = $this->_customerFactory->create()->load($account->getCustomerId());
			$accountCollection = $this->_accountFactory->create()->getCollection()
				->addFieldToFilter('customer_id', $account->getCustomerId());
			$numOfAccount      = $accountCollection->getSize();
			if (($account->getId() && $numOfAccount > 1) || (!$account->getId() && $numOfAccount > 0)) {
				$this->messageManager->addError(__('The customer "%1" has registed as an affiliate already.', $customer->getEmail()));
				$this->_getSession()->setData('affiliate_account_data', $data);

				$resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

				return $resultRedirect;
			}

			$account->setData('parent', null);
			if (isset($data['parent']) && is_numeric($data['parent'])) {
				$parent = $this->_accountFactory->create()->load($data['parent']);
				if ($parent && $parent->getId()) {
					$account->setData('parent', $parent->getId());
				} else {
					$this->messageManager->addNoticeMessage(__('Cannot find account referred.'));
				}
			}

			try {
				$account->save();
				$this->_getSession()->unsetData('account_customer_id');
				$this->messageManager->addSuccess($account->isObjectNew() ? __('The Account has been created successfully.') : __('The Account has been saved successfully.'));
				$this->_getSession()->setData('affiliate_account_data', false);
				if ($this->getRequest()->getParam('back')) {
					$resultRedirect->setPath('affiliate/*/edit', ['id' => $account->getId()]);

					return $resultRedirect;
				}

				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the Account.'));
			}
			$this->_getSession()->setData('affiliate_account_data', $data);
			$resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

			return $resultRedirect;
		}
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}
}
