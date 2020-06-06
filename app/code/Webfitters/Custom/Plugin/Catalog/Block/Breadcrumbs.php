<?php
namespace Webfitters\Custom\Plugin\Catalog\Block;

class Breadcrumbs {

    public function afterSetLayout(\Magento\Catalog\Block\Product\View $subject, $result) {
        $subject->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
        return $result;
    }

}