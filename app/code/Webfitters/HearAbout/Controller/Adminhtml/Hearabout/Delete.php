<?php
namespace Webfitters\HearAbout\Controller\Adminhtml\HearAbout;

class Delete extends \Magento\Backend\App\Action {

	protected $page;
	protected $hearabout;
	protected $messages;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $page,
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout
	) {
		parent::__construct($context);
		$this->page = $page;
		$this->hearabout = $hearabout;
		$this->messages = $context->getMessageManager();
	}

	public function execute() {
		if(!$this->getRequest()->getParam('id')){
			$this->messages->addError(__('An id must be specified to delete.'));
			return $this->_redirect('hearabout/hearabout/index');
		}
		$hearabout = $this->hearabout->create()->load($this->getRequest()->getParam('id'));
		$hearabout->delete();
		$this->messages->addSuccess(__('Hear about source deleted.'));
		return $this->_redirect('hearabout/hearabout/index');

	}

}