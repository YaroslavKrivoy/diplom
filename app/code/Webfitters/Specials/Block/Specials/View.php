<?php
namespace Webfitters\Specials\Block\Specials;

class View extends \Magento\Catalog\Block\Category\View {

	protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);             
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle('Specials');
        }
        return $this;
    }

    public function getCmsBlockHtml() {
    	return '';
    }

    public function isProductMode() {
    	return true;
    }

    public function isMixedMode() {
    	return false;
    }

    public function isContentMode() {
    	return false;
    }

    public function getIdentities() {
    	return [];
    }

}