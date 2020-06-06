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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Save
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
 */
class Save extends \Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
{
	/**
	 * Date filter
	 *
	 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
	 */
	protected $_dateFilter;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Mageplaza\Affiliate\Model\WithdrawFactory $withdrawFactory
	 * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Mageplaza\Affiliate\Model\WithdrawFactory $withdrawFactory,
		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
	)
	{
		$this->_dateFilter = $dateFilter;
		parent::__construct($context, $resultPageFactory, $coreRegistry, $withdrawFactory);
	}

	/**
	 * run the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$data           = $this->getRequest()->getPost('withdraw');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$data     = $this->_filterData($data);
			$withdraw = $this->_initWithdraw();
			$withdraw->setData($data);

			$redirectBack = $withdraw->getId() ? 'edit' : 'create';

			if ($this->getRequest()->getParam('back')) {
				$withdraw->setStatus(\Mageplaza\Affiliate\Model\Withdraw\Status::COMPLETE);
			}

			$this->_eventManager->dispatch('affiliate_withdraw_prepare_save', [
				'withdraw' => $withdraw,
				'action'   => $this
			]);

			try {
				$withdraw->save();

				$this->messageManager->addSuccess(__('The Withdraw has been saved.'));
				$this->_getSession()->setAffiliateWithdrawData(false);
				$this->_getSession()->unsetData('withdraw_customer_id');

				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the Withdraw.'));
			}
			$this->_getSession()->setAffiliateWithdrawData($data);

			$resultRedirect->setPath('affiliate/*/' . $redirectBack, ['_current' => true]);

			return $resultRedirect;
		}
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}

	/**
	 * filter values
	 *
	 * @param array $data
	 * @return array
	 */
	protected function _filterData($data)
	{
		$inputFilter = new \Zend_Filter_Input(['requested_at' => $this->_dateFilter,], [], $data);
		$data        = $inputFilter->getUnescaped();

		return $data;
	}
}
