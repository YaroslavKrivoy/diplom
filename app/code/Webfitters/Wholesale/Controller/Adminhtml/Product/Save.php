<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Product;

class Save extends \Magento\Backend\App\Action {

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
        } else {
            $product = $this->product->create();
        }
        $product->setCategoryId($this->getRequest()->getParam('category_id'));
        $product->setDescription($this->getRequest()->getParam('description'));
        $product->setItemNumber($this->getRequest()->getParam('item_number'));
        $product->setSize($this->getRequest()->getParam('size'));
        $product->save();
        echo json_encode(array('error' => 'false', 'message' => 'Product successfully saved.', 'product' => array(
            'id' => $product->getId(),
            'category_id' => $product->getCategoryId(),
            'description' => $product->getDescription(),
            'item_number' => $product->getItemNumber(),
            'size' => $product->getSize()
        )));
        die();
    }
}