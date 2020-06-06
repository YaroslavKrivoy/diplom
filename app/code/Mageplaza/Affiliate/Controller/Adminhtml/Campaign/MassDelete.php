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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class MassDelete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class MassDelete extends \Magento\Backend\App\Action
{
	/**
	 * Mass Action Filter
	 *
	 * @var \Magento\Ui\Component\MassAction\Filter
	 */
	protected $_filter;

	/**
	 * Collection Factory
	 *
	 * @var \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory
	 */
	protected $_collectionFactory;

	/**
	 * constructor
	 *
	 * @param \Magento\Ui\Component\MassAction\Filter $filter
	 * @param \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Magento\Ui\Component\MassAction\Filter $filter,
		\Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory,
		\Magento\Backend\App\Action\Context $context
	)
	{
		$this->_filter            = $filter;
		$this->_collectionFactory = $collectionFactory;
		parent::__construct($context);
	}


	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$collection = $this->_filter->getCollection($this->_collectionFactory->create());

		$delete = 0;
		foreach ($collection as $item) {
			/** @var \Mageplaza\Affiliate\Model\Campaign $item */
			try {
				$item->delete();
				$delete++;
			} catch (\Exception $e) {
			}
		}
		$this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $delete));
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

		return $resultRedirect->setPath('*/*/');
	}
}
