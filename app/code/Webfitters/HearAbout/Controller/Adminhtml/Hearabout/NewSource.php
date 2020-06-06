<?php
namespace Webfitters\HearAbout\Controller\Adminhtml\HearAbout;

class NewSource extends \Magento\Backend\App\Action {

	protected $page;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $page
	) {
		parent::__construct($context);
		$this->page = $page;
	}

	public function execute() {
		$page = $this->page->create();
		$page->getConfig()->getTitle()->prepend((__('New Hear About Source')));
		return $page;
	}

}