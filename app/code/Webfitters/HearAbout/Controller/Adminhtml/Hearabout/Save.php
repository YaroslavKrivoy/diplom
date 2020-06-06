<?php
namespace Webfitters\HearAbout\Controller\Adminhtml\HearAbout;

class Save extends \Magento\Backend\App\Action {

	protected $page;
	protected $messages;
	protected $hearabout;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $page,
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout
	) {
		parent::__construct($context);
		$this->messages = $context->getMessageManager();
		$this->page = $page;
		$this->hearabout = $hearabout;
	}

	public function execute() {
		$post = (object)$this->getRequest()->getPostValue('hear_about');
		if(!isset($post->name) || $post->name == ''){
			$this->messages->addError(__('Source name is a required field.'));
			return $this->_redirect($this->_redirect->getRefererUrl());
		}
		$hearabout = $this->hearabout->create();
		if(isset($post->id)){
			$hearabout = $hearabout->load($post->id);
		}
		$hearabout->setName($post->name);
		$hearabout->save();
		$this->messages->addSuccess(__('Source successfully saved.'));
		return $this->_redirect('hearabout/hearabout/index');
	}

}