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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class MassComplete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class MassComplete extends \Magento\Backend\App\Action
{
	/**
	 * @type \Mageplaza\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @type \Magento\Ui\Component\MassAction\Filter
	 */
	protected $_filter;

	/**
	 * Constructor
	 *
	 * @param \Mageplaza\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Ui\Component\MassAction\Filter $filter
	 */
	public function __construct(
		\Mageplaza\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory,
		\Magento\Backend\App\Action\Context $context,
		\Magento\Ui\Component\MassAction\Filter $filter
	)
	{
		$this->_transactionFactory = $transactionFactory;
		$this->_filter             = $filter;
		parent::__construct($context);
	}

	/**
	 * execute the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$collection           = $this->_filter->getCollection($this->_transactionFactory->create());
		$transactionCompleted = 0;

		foreach ($collection->getItems() as $transaction) {
			try {
				$transaction->complete();
				$transactionCompleted++;
			} catch (\Exception $e) {

			}
		}

		$this->messageManager->addSuccess(
			__('A total of %1 record(s) have been completed.', $transactionCompleted)
		);

		return $this->resultRedirectFactory->create()->setPath('affiliate/*/');
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::transaction');
	}
}
