<?php
namespace Webfitters\Custom\Block;

class NutritionBlock extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface {
    
    protected $_coreRegistry;
    protected $filter;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filter,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->filter = $filter;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    public function getProduct() {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product : null;
    }

    public function filterContent($content = ''){
        return $this->filter->getPageFilter()->filter($content);
    }

    public function setTabTitle() {
        $this->setTitle(__('Nutrition Info'));
    }

    public function getIdentities() {
        return [];
    }
}
