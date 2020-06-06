<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule;

use Aheadworks\Autorelated\Model\Source\Type;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Registry;
use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Validator as ConditionValidator;

/**
 * Class CurrentPageObject
 *
 * @package Aheadworks\Autorelated\Model\Rule
 */
class CurrentPageObject
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ConditionValidator
     */
    private $conditionValidator;

    /**
     * @var array
     */
    private $products = [];

    /**
     * @var array
     */
    private $categories = [];

    /**
     * @param CheckoutSession $checkoutSession
     * @param RequestInterface $request
     * @param CatalogHelper $catalogHelper
     * @param Registry $registry
     * @param ConditionValidator $conditionValidator
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        RequestInterface $request,
        CatalogHelper $catalogHelper,
        Registry $registry,
        ConditionValidator $conditionValidator
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
        $this->conditionValidator = $conditionValidator;
    }

    /**
     * Return current product id
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCurrentProductIdForBlock($rule, $blockType)
    {
        if (array_key_exists($rule->getId(), $this->products)) {
            $currentProductId = $this->products[$rule->getId()];
        } else {
            $currentProductId = null;
            switch ($blockType) {
                case Type::PRODUCT_BLOCK_TYPE:
                    $productFromRegistry = $this->registry->registry('current_product');
                    if ($productFromRegistry instanceof ProductInterface) {
                        $currentProductId = $productFromRegistry->getId();
                    } elseif ($this->request->getParam('product_id')) {
                        $currentProductId = $this->request->getParam('product_id');
                    } elseif ($this->request->getParam('id')) {
                        $currentProductId = $this->request->getParam('id');
                    } elseif ($this->request->getParam('product')) {
                        //for case ARP is called from AW Ajaxcartpro "add to cart" action
                        $currentProductId = $this->request->getParam('product');
                    }
                    break;
                case Type::CART_BLOCK_TYPE:
                    $quoteModel = $this->checkoutSession->getQuote();
                    if (!$quoteModel->getId()) {
                        return null;
                    }
                    $quoteModel->getItemsCollection()->getSelect()->order('main_table.price DESC');
                    foreach ($quoteModel->getItemsCollection()->getData() as $item) {
                        if ($this->conditionValidator->isProductValid($rule, $item['product_id'])) {
                            $currentProductId = $item['product_id'];
                            break;
                        }
                    }
                    break;
            }
            $this->products[$rule->getId()] = $currentProductId;
        }

        return $currentProductId;
    }

    /**
     * Return current category id
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCurrentCategoryIdForBlock($rule, $blockType)
    {
        if (array_key_exists($rule->getId(), $this->categories)) {
            $currentCategoryId = $this->categories[$rule->getId()];
        } else {
            $currentCategoryId = null;

            switch ($blockType) {
                case Type::PRODUCT_BLOCK_TYPE:
                    $currentCategory = $this->catalogHelper->getCategory();
                    if (is_object($currentCategory)) {
                        $currentCategoryId = $currentCategory->getEntityId();
                    }
                    break;
                case Type::CATEGORY_BLOCK_TYPE:
                    $currentCategoryId = $this->request->getParam('id');
                    break;
            }
            $this->categories[$rule->getId()] = $currentCategoryId;
        }
        return $currentCategoryId;
    }
}
