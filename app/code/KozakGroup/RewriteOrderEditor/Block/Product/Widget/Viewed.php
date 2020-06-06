<?php

namespace KozakGroup\RewriteOrderEditor\Block\Product\Widget;

/**
 * Reports Recently Viewed Products Widget
 * @deprecated 100.2.0 Since new frontend base widgets are provided
 * @see \Magento\Catalog\Block\Widget\RecentlyViewed
 */
class Viewed extends \Magento\Reports\Block\Product\Widget\Viewed implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    public function __construct(
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Reports\Model\Product\Index\Factory $indexFactory,
        array $data = [])
    {
        $this->_catalogSession = $catalogSession;
        parent::__construct($context, $productVisibility, $indexFactory, $data);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);
    }

    public function removeCurrentProductFromRecentlyViewed(array $recentlyViewedProducts)
    {
        $parentProductId = $this->_catalogSession->getlastViewedProductId();

        /** @var Product $product */
        foreach ($recentlyViewedProducts as $product) {
            if ((count($recentlyViewedProducts) == 1) && ($product->getId() == $parentProductId)) {
                return [];
            } elseif ($product->getId() == $parentProductId) {
                unset($recentlyViewedProducts[$product->getId()]);
            }
        }

        return $recentlyViewedProducts;
    }
}
