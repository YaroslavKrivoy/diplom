<?php
namespace Webfitters\Wholesale\Controller\Type;

class Index extends \Magento\Framework\App\Action\Action {

	protected $type;
	protected $forwardFactory;
	protected $page;
	protected $registry;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Result\PageFactory $page,
        \Webfitters\Wholesale\Model\TypeFactory $type
	) {
		parent::__construct($context);
		$this->type = $type;
		$this->forwardFactory = $forwardFactory;
		$this->page = $page;
		$this->registry = $registry;
	}

	public function execute(){
		$name = str_replace('-', ' ', urldecode($this->_request->getParam('type')));
		$types = $this->type->create()->getCollection()->addFieldToFilter('title', array(array('like' => '%'.$name.'%')));
		if($types->getSize() == 0){
			return $this->notFound();
		}
		$type = $types->getFirstItem();
		$this->registry->register('type', $type);
		$page = $this->page->create();
		$page->getConfig()->getTitle()->set($type->getTitle());
        return $page;
	}

	protected function notFound(){
		return $this->forwardFactory->create()->forward('noroute');
	}

}