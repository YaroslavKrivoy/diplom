<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Categories;

class Delete extends \Magento\Backend\App\Action {

    protected $category;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webfitters\Wholesale\Model\CategoryFactory $category
    ) {
         parent::__construct($context);
         $this->category = $category;
    }

    public function execute() {
        if(!$this->getRequest()->isAjax()){
            return false;
        }
        if($this->getRequest()->getParam('id') && $this->getRequest()->getParam('id') != ''){
            $category = $this->category->create()->load($this->getRequest()->getParam('id'), 'id');
            $category->delete();
            echo json_encode(array('error' => false, 'message' => 'Category successfully deleted'));
        } 
        echo json_encode(array('error' => true, 'message' => 'Unable to locate that category.'));
        die();
    }
}