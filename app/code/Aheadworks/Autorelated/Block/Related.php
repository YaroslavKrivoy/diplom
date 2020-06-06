<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block;

use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Template;
use Aheadworks\Autorelated\Api\BlockRepositoryInterface;
use Aheadworks\Autorelated\Model\Config;
use Aheadworks\Autorelated\Model\BlockReplacementManager;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\FormKey as FormKeyView;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\Autorelated\Model\Rule\CurrentPageObject;
use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Class Related
 *
 * @package Aheadworks\Autorelated\Block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Related extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Autorelated::block.phtml';

    /**
     * @var int|null
     */
    private $blockPosition;

    /**
     * @var int|null
     */
    private $blockType;

    /**
     * @var array|null
     */
    private $blocks;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blocksRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var PostHelper
     */
    private $postHelper;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var CartHelper
     */
    private $cartHelper;

    /**
     * @var EncoderInterface
     */
    private $urlEncoder;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockReplacementManager
     */
    private $blockReplacementManager;

    /**
     * @var CurrentPageObject
     */
    private $currentPageObject;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param Context $context
     * @param BlockRepositoryInterface $blocksRepository
     * @param ProductRepositoryInterface $productRepository
     * @param PostHelper $postHelper
     * @param ImageBuilder $imageBuilder
     * @param CartHelper $cartHelper
     * @param EncoderInterface $urlEncoder
     * @param Manager $moduleManager
     * @param FormKey $formKey
     * @param Config $config
     * @param BlockReplacementManager $blockReplacementManager
     * @param CurrentPageObject $currentPageObject
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        BlockRepositoryInterface $blocksRepository,
        ProductRepositoryInterface $productRepository,
        PostHelper $postHelper,
        ImageBuilder $imageBuilder,
        CartHelper $cartHelper,
        EncoderInterface $urlEncoder,
        Manager $moduleManager,
        FormKey $formKey,
        Config $config,
        BlockReplacementManager $blockReplacementManager,
        CurrentPageObject $currentPageObject,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blocksRepository = $blocksRepository;
        $this->productRepository = $productRepository;
        $this->postHelper = $postHelper;
        $this->imageBuilder = $imageBuilder;
        $this->cartHelper = $cartHelper;
        $this->urlEncoder = $urlEncoder;
        $this->moduleManager = $moduleManager;
        $this->formKey = $formKey;
        $this->config = $config;
        $this->blockReplacementManager = $blockReplacementManager;
        $this->currentPageObject = $currentPageObject;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }

    /**
     * Return blocks for current block position and type
     *
     * @return \Aheadworks\Autorelated\Api\Data\BlockInterface[]
     */
    public function getBlocks()
    {
        if (null === $this->blocks) {
            $this->blocks = $this->blocksRepository
                ->getList(
                    $this->getBlockType(),
                    $this->getBlockPosition(),
                    $this->config->isShowingMultipleBlocksAllowed()
                )->getItems();
        }

        return $this->blocks;
    }

    /**
     * Return PostHelper object
     *
     * @return PostHelper
     */
    public function getPostDataHelper()
    {
        return $this->postHelper;
    }

    /**
     * Is grid template
     *
     * @param int $templateId
     * @return bool
     */
    public function isGridTemplate($templateId)
    {
        if ($templateId == Template::GRID) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return product model by product id
     *
     * @param int $productId
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Return category model by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategoryById($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Return product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Return add to cart url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }
        return $this->cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Return product url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }
        return '#';
    }

    /**
     * Return product price value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                PricingRender::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices', 'use_link_for_as_low_as' => true]]
            );
        }

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => PricingRender::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }

    /**
     * Encode url
     *
     * @param string $url
     * @return string
     */
    public function encodeUrl($url)
    {
        return $this->urlEncoder->encode($url);
    }

    /**
     * Retrieve send click statistics params for widget initialization
     *
     * @param BlockInterface $block
     * @return array
     */
    public function getSendClickStatisticsMageInitParams($block)
    {
        return [
            'awArpSendClickStatistics' => [
                'ruleId' => $block->getRule()->getId(),
                'url' => $this->_urlBuilder->getUrl('autorelated/click/process')
            ]
        ];
    }

    /**
     * Encode params to json
     *
     * @param array $params
     * @return string
     */
    public function jsonEncode($params)
    {
        return \Zend_Json::encode($params);
    }
    /**
     * Retrieve corresponding rule id for specific block
     *
     * @param \Aheadworks\Autorelated\Api\Data\BlockInterface $arpBlock
     * @return int
     */
    public function getRuleIdForBlock($arpBlock)
    {
        return $arpBlock->getRule()->getId();
    }

    /**
     * Remove native related block if necessary
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        switch ($this->getBlockPosition()) {
            case Position::PRODUCT_INSTEAD_NATIVE_RELATED_BLOCK:
                if ($this->getBlocks()) {
                    $this->blockReplacementManager->setIsArpUsedInsteadFlag(
                        $this->getLayout()->getBlock('catalog.product.related')
                    );
                }
                break;
            case Position::PRODUCT_INSTEAD_NATIVE_UP_SELLS_BLOCK:
                if ($this->getBlocks()) {
                    $this->blockReplacementManager->setIsArpUsedInsteadFlag(
                        $this->getLayout()->getBlock('product.info.upsell')
                    );
                }
                break;
            case Position::CART_INSTEAD_NATIVE_CROSSSELLS_BLOCK:
                if ($this->getBlocks()) {
                    $this->blockReplacementManager->setIsArpUsedInsteadFlag(
                        $this->getLayout()->getBlock('checkout.cart.crosssell')
                    );
                }
                break;
            default:
        }

        return parent::_prepareLayout();
    }

    /**
     * Check Product has URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return block position
     *
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function getBlockPosition()
    {
        if ($this->blockPosition === null) {
            switch ($this->getNameInLayout()) {
                case 'awarp_content_top_product':
                    $this->blockPosition = Position::PRODUCT_CONTENT_TOP;
                    break;
                case 'awarp_content_bottom_product':
                    $this->blockPosition = Position::PRODUCT_CONTENT_BOTTOM;
                    break;
                case 'awarp_content_sidebar_top_product':
                    $this->blockPosition = Position::PRODUCT_SIDEBAR_TOP;
                    break;
                case 'awarp_content_sidebar_bottom_product':
                    $this->blockPosition = Position::PRODUCT_SIDEBAR_BOTTOM;
                    break;
                case 'awarp_related_inside_product':
                    $this->blockPosition = Position::PRODUCT_INSTEAD_NATIVE_RELATED_BLOCK;
                    break;
                case 'awarp_related_before_product':
                    $this->blockPosition = Position::PRODUCT_BEFORE_NATIVE_RELATED_BLOCK;
                    break;
                case 'awarp_related_after_product':
                    $this->blockPosition = Position::PRODUCT_AFTER_NATIVE_RELATED_BLOCK;
                    break;
                case 'awarp_up_sells_inside_product':
                    $this->blockPosition = Position::PRODUCT_INSTEAD_NATIVE_UP_SELLS_BLOCK;
                    break;
                case 'awarp_up_sells_before_product':
                    $this->blockPosition = Position::PRODUCT_BEFORE_NATIVE_UP_SELLS_BLOCK;
                    break;
                case 'awarp_up_sells_after_product':
                    $this->blockPosition = Position::PRODUCT_AFTER_NATIVE_UP_SELLS_BLOCK;
                    break;

                case 'awarp_content_top_shopping_cart':
                    $this->blockPosition = Position::CART_CONTENT_TOP;
                    break;
                case 'awarp_content_bottom_shopping_cart':
                    $this->blockPosition = Position::CART_CONTENT_BOTTOM;
                    break;
                case 'awarp_crosssell_before_shopping_cart':
                    $this->blockPosition = Position::CART_BEFORE_NATIVE_CROSSSELLS_BLOCK;
                    break;
                case 'awarp_crosssell_instead_shopping_cart':
                    $this->blockPosition = Position::CART_INSTEAD_NATIVE_CROSSSELLS_BLOCK;
                    break;
                case 'awarp_crosssell_after_shopping_cart':
                    $this->blockPosition = Position::CART_AFTER_NATIVE_CROSSSELLS_BLOCK;
                    break;

                case 'awarp_content_top_category':
                    $this->blockPosition = Position::CATEGORY_CONTENT_TOP;
                    break;
                case 'awarp_category_page_bottom':
                    $this->blockPosition = Position::CATEGORY_CONTENT_BOTTOM;
                    break;
                case 'awarp_custom':
                    $this->blockPosition = Position::CUSTOM;
                    break;
            }
        }
        return $this->blockPosition;
    }

    /**
     * Rreturn block type
     *
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function getBlockType()
    {
        if ($this->blockType === null) {
            switch ($this->getNameInLayout()) {
                case 'awarp_content_top_product':
                case 'awarp_content_bottom_product':
                case 'awarp_content_sidebar_top_product':
                case 'awarp_content_sidebar_bottom_product':
                case 'awarp_related_inside_product':
                case 'awarp_related_before_product':
                case 'awarp_related_after_product':
                case 'awarp_up_sells_before_product':
                case 'awarp_up_sells_inside_product':
                case 'awarp_up_sells_after_product':
                    $this->blockType = Type::PRODUCT_BLOCK_TYPE;
                    break;

                case 'awarp_content_top_shopping_cart':
                case 'awarp_content_bottom_shopping_cart':
                case 'awarp_crosssell_before_shopping_cart':
                case 'awarp_crosssell_instead_shopping_cart':
                case 'awarp_crosssell_after_shopping_cart':
                    $this->blockType = Type::CART_BLOCK_TYPE;
                    break;

                case 'awarp_content_top_category':
                case 'awarp_category_page_bottom':
                    $this->blockType = Type::CATEGORY_BLOCK_TYPE;
                    break;
                case 'awarp_custom':
                    $this->blockType = Type::CUSTOM_BLOCK_TYPE;
                    break;
            }
        }
        return $this->blockType;
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return \Zend_Json::encode($this->formKey->getFormKey());
    }

    /**
     * Get form key html
     *
     * @return string
     */
    public function getFormKeyHtml()
    {
        $formKeyHtml = '';
        $formKeyBlock = $this->getLayout()->getBlock('formkey');
        if (!$formKeyBlock) {
            $formKeyBlock = $this->getLayout()->createBlock(
                FormKeyView::class,
                'formkey'
            );
        }
        if (is_object($formKeyBlock)) {
            $formKeyHtml = $formKeyBlock->toHtml();
        }
        return $formKeyHtml;
    }

    /**
     * Retrieve data mage init for specific block
     *
     * @param \Aheadworks\Autorelated\Api\Data\BlockInterface $arpBlock
     * @return string
     */
    public function getDataMageInitForBlock($arpBlock)
    {
        return $this->isGridTemplate($arpBlock->getRule()->getTemplateId())
            ? '{"awArpGrid": {"rows": "' . $arpBlock->getRule()->getGridRow() . '"}}'
            : '{"awArpSlider": {}}';
    }

    /**
     * Retrieve additional css classes for specific block
     *
     * @param \Aheadworks\Autorelated\Api\Data\BlockInterface $arpBlock
     * @return string
     */
    public function getAdditionalCssClassesForBlock($arpBlock)
    {
        return $this->isGridTemplate($arpBlock->getRule()->getTemplateId())
            ? 'aw-arp-container--grid'
            : 'aw-arp-container--slider';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [
            \Aheadworks\Autorelated\Model\Rule::CACHE_LIST_TAG,
            \Magento\Catalog\Model\Product::CACHE_TAG,
            \Magento\Catalog\Model\Category::CACHE_TAG
        ];

        $identities = array_merge($identities, $this->getRelatedProductsIdentities());
        $identities = array_merge($identities, $this->getCurrentPageObjectIdentities());

        $identities = array_unique($identities);

        return $identities;
    }

    /**
     * Retrieve related products identities for all visible rules
     *
     * @return array
     */
    private function getRelatedProductsIdentities()
    {
        $relatedProductsIdentities = [];
        if ($this->getBlocks()) {
            foreach ($this->getBlocks() as $blockToShow) {
                $products = $blockToShow->getProducts();
                foreach ($products as $product) {
                    $relatedProductsIdentities = array_merge(
                        $relatedProductsIdentities,
                        $product->getIdentities()
                    );
                }
            }
        }
        return $relatedProductsIdentities;
    }

    /**
     * Retrieve product identities array
     *
     * @param int $productId
     * @return array
     */
    private function getProductIdentities($productId)
    {
        $productIdentities = [];
        $productModel = $this->getProductById($productId);
        if (is_object($productModel)) {
            $productIdentities = $productModel->getIdentities();
        }
        return $productIdentities;
    }

    /**
     * Retrieve identities array of significant objects related to current page
     *
     * @return array
     */
    private function getCurrentPageObjectIdentities()
    {
        $currentPageObjectIdentities = [];
        $blockType = $this->getBlockType();
        if ($this->getBlocks()) {
            foreach ($this->getBlocks() as $blockToShow) {
                $blockRule = $blockToShow->getRule();
                $currentPageObjectIdentities = array_merge(
                    $currentPageObjectIdentities,
                    $this->getCurrentPageObjectIdentitiesForRule($blockRule, $blockType)
                );
            }
        }
        return $currentPageObjectIdentities;
    }

    /**
     * Retrieve identities array of significant objects related to current page for specific rule
     *
     * @param RuleInterface $blockRule
     * @param int $blockType
     * @return array
     */
    private function getCurrentPageObjectIdentitiesForRule($blockRule, $blockType)
    {
        $currentPageObjectIdentitiesForRule = array_merge(
            $this->getCurrentProductIdentities($blockRule, $blockType),
            $this->getCurrentCategoryIdentities($blockRule, $blockType)
        );
        return $currentPageObjectIdentitiesForRule;
    }

    /**
     * Retrieve identities array of current page product for specific rule
     *
     * @param RuleInterface $blockRule
     * @param int $blockType
     * @return array
     */
    private function getCurrentProductIdentities($blockRule, $blockType)
    {
        $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($blockRule, $blockType);
        return $this->getProductIdentities($currentProductId);
    }

    /**
     * Retrieve identities array of current page category for specific rule
     *
     * @param RuleInterface $blockRule
     * @param int $blockType
     * @return array
     */
    private function getCurrentCategoryIdentities($blockRule, $blockType)
    {
        $categoryIdentities = [];
        $currentCategoryId = $this->currentPageObject->getCurrentCategoryIdForBlock($blockRule, $blockType);
        $categoryModel = $this->getCategoryById($currentCategoryId);
        if (is_object($categoryModel)) {
            $categoryIdentities = $categoryModel->getIdentities();
        }
        return $categoryIdentities;
    }
}
