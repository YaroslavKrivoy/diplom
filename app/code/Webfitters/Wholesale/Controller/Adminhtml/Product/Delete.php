<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Product;

class Delete extends \Magento\Backend\App\Action {

    protected $product;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webfitters\Wholesale\Model\ProductFactory $product
    ) {
         parent::__construct($context);
         $this->product = $product;
    }

    public function execute() {
        if(!$this->getRequest()->isAjax()){
            return false;
        }
        if($this->getRequest()->getParam('id') && $this->getRequest()->getParam('id') != ''){
            $product = $this->product->create()->load($this->getRequest()->getParam('id'), 'id');
            $product->delete();
            echo json_encode(array('error' => false, 'message' => 'Product successfully deleted'));
            die();
        } 
        echo json_encode(array('error' => true, 'message' => 'Unable to locate that product.'));
        die();
    }
}