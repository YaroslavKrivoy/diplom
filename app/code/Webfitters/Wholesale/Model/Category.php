<?php 
namespace Webfitters\Wholesale\Model;

class Category extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

	const CACHE_TAG = 'webfitters_wholesale_cat';
	protected $product;

	public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Webfitters\Wholesale\Model\ProductFactory $product,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
		$this->product = $product;
    }

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\ResourceModel\Category');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getProducts(){
		return $this->product->create()->getCollection()->addFieldToFilter('category_id', $this->getId());
	}

}