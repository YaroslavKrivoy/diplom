<?php
namespace Webfitters\Featured\Block;

class Product extends \Magento\Catalog\Block\Product\ListProduct {

	protected $products;
	protected $visibility;
	protected $image;
	protected $status;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $post,
        \Magento\Catalog\Model\Layer\Resolver $resolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $category,
        \Magento\Framework\Url\Helper\Data $url,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products, 
		\Magento\Catalog\Model\Product\Attribute\Source\Status $status,
    	\Magento\Catalog\Model\Product\Visibility $visibility,
		array $data = []
	){
		$this->products=$products;
		$this->status = $status;
		$this->visibility = $visibility;
		parent::__construct($context, $post, $resolver, $category, $url, $data);
	}

	public function getFeatured(){
		$collection = 
			$this->products->create()
				->addAttributeToFilter('featured', '1')
				->addAttributeToFilter('status', ['in' => $this->status->getVisibleStatusIds()])
				->setVisibility($this->visibility->getVisibleInSiteIds())->setPageSize(10)->setCurPage(1)
				->addAttributeToSelect('*');
    	return $collection;
	}

}
?>