<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Categories;

class Save extends \Magento\Backend\App\Action {

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
        } else {
            $category = $this->category->create();
        }
        $category->setTypeId($this->getRequest()->getParam('type_id'));
        $category->setTitle($this->getRequest()->getParam('title'));
        $category->save();
        echo json_encode(array('error' => 'false', 'message' => 'Category successfully saved.', 'category' => array(
            'title' => $category->getTitle(),
            'id' => $category->getId(),
            'type_id' => $category->getTypeId()
        )));
        die();
    }
}