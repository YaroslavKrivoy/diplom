<?php
namespace Mageplaza\Affiliate\Block;

class Totals extends \Magento\Framework\View\Element\Template
{
	public function initTotals()
	{
		$parent = $this->getParentBlock();
		$source = $parent->getSource();
		if($source->getAffiliateDiscountAmount() != 0){
			$parent->addTotal(new \Magento\Framework\DataObject([
				'code'       => 'affiliate_discount',
				'value'      => $source->getAffiliateDiscountAmount(),
				'base_value' => $source->getBaseAffiliateDiscountAmount(),
				'label'      => __('Affiliate Discount')
			]));
		}
		return $this;
	}
}
