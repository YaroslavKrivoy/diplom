<?php 
namespace Webfitters\Wholesale\Model;

class Type extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

	const CACHE_TAG = 'webfitters_wholesale_type';
	protected $category;

	public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Webfitters\Wholesale\Model\CategoryFactory $category,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
		$this->category = $category;
    }

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\ResourceModel\Type');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getCategories(){
		return $this->category->create()->getCollection()->addFieldToFilter('type_id', $this->getId());
	}

	public function getSlug(){
		$name = $this->getTitle();
		return strtolower(str_replace(' ', '-', $name));
	}

}