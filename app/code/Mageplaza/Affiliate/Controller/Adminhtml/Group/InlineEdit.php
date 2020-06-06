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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Group;

/**
 * Class InlineEdit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Group
 */
class InlineEdit extends \Magento\Backend\App\Action
{
	/**
	 * JSON Factory
	 *
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_jsonFactory;

	/**
	 * Group Factory
	 *
	 * @var \Mageplaza\Affiliate\Model\GroupFactory
	 */
	protected $_groupFactory;

	/**
	 * constructor
	 *
	 * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
	 * @param \Mageplaza\Affiliate\Model\GroupFactory $groupFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
		\Mageplaza\Affiliate\Model\GroupFactory $groupFactory,
		\Magento\Backend\App\Action\Context $context
	)
	{
		$this->_jsonFactory  = $jsonFactory;
		$this->_groupFactory = $groupFactory;
		parent::__construct($context);
	}

	/**
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Framework\Controller\Result\Json $resultJson */
		$resultJson = $this->_jsonFactory->create();
		$error      = false;
		$messages   = [];
		$postItems  = $this->getRequest()->getParam('items', []);
		if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
			return $resultJson->setData([
				'messages' => [__('Please correct the data sent.')],
				'error'    => true,
			]);
		}
		foreach (array_keys($postItems) as $groupId) {
			/** @var \Mageplaza\Affiliate\Model\Group $group */
			$group = $this->_groupFactory->create()->load($groupId);
			try {
				$groupData = $postItems[$groupId];//todo: handle dates
				$group->addData($groupData);
				$group->save();
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$messages[] = $this->getErrorWithGroupId($group, $e->getMessage());
				$error      = true;
			} catch (\RuntimeException $e) {
				$messages[] = $this->getErrorWithGroupId($group, $e->getMessage());
				$error      = true;
			} catch (\Exception $e) {
				$messages[] = $this->getErrorWithGroupId(
					$group,
					__('Something went wrong while saving the Group.')
				);
				$error      = true;
			}
		}

		return $resultJson->setData([
			'messages' => $messages,
			'error'    => $error
		]);
	}

	/**
	 * Add Group id to error message
	 *
	 * @param \Mageplaza\Affiliate\Model\Group $group
	 * @param string $errorText
	 * @return string
	 */
	protected function getErrorWithGroupId(\Mageplaza\Affiliate\Model\Group $group, $errorText)
	{
		return '[Group ID: ' . $group->getId() . '] ' . $errorText;
	}
}
