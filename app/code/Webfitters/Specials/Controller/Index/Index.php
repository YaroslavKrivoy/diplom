<?php
namespace Webfitters\Specials\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {

	protected $page;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $page
	) {
		parent::__construct($context);
		$this->page = $page;
	}

	public function execute(){
		if ($this->_request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED)) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
        }
        $page = $this->page->create();
        $page->addPageLayoutHandles(['type' => strtok('layered_without_children', '_')], null, false);
        $page->addPageLayoutHandles(['type' => 'layered_without_children'], null, false);
        $page->getConfig()->addBodyClass('page-products')->addBodyClass('catalog-category-view')->addBodyClass('categorypath-specials')->addBodyClass('category-specials-index-index');
        return $page;
	}

}