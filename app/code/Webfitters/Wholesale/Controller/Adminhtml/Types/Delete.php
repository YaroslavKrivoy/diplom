<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Types;

class Delete extends \Magento\Backend\App\Action {

    protected $types;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webfitters\Wholesale\Model\TypeFactory $types
    ) {
         parent::__construct($context);
         $this->types = $types;
    }

    public function execute() {
        if(!$this->getRequest()->isAjax()){
            return false;
        }
        if($this->getRequest()->getParam('id') && $this->getRequest()->getParam('id') != ''){
            $type = $this->types->create()->load($this->getRequest()->getParam('id'), 'id');
            $type->delete();
            echo json_encode(array('error' => 'false', 'message' => 'Type successfully deleted.'));
            die();
        } 
        echo json_encode(array('error' => 'true', 'message' => 'Type not specified for deletion.'));
        die();
    }

}