<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Rule\Related;

use Aheadworks\Autorelated\Model\Rule\Related\Validator;
use Aheadworks\Autorelated\Model\Source\Type as SourceType;
use Aheadworks\Autorelated\Model\Rule;
use Aheadworks\Autorelated\Model\Rule\Related\Product as RelatedProduct;
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProduct as RelatedCategoryProduct;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Aheadworks\Autorelated\Model\Rule\CurrentPageObject;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Autorelated\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as QuoteItemCollection;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection as LinkProductCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Aheadworks\Autorelated\Model\Source\ProductConditionType;

/**
 * Test for \Aheadworks\Autorelated\Model\Rule\Related\Validator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var Visibility|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productVisibilityMock;

    /**
     * @var ProductCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productCollectionMock;

    /**
     * @var CheckoutSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var CurrentPageObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currentPageObjectMock;

    /**
     * @var ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var TypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleTypeResolverMock;

    /**
     * @var CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->productVisibilityMock = $this->getMockBuilder(Visibility::class)
            ->setMethods(['getVisibleInSiteIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->productCollectionMock = $this->getMockBuilder(ProductCollection::class)
            ->setMethods([
                'resetCollection',
                'addStoreFilter',
                'setVisibility',
                'addIdFilter',
                'addProductSorting',
                'setPageSize',
                'setCurPage',
                'load',
                'getLoadedProductIds',
                'addInStockFilter',
                'addAttributeToSelect',
                'addAttributeToFilter',
                'getItems',
                'addUrlRewrite'
            ])->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSessionMock = $this->getMockBuilder(CheckoutSession::class)
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->currentPageObjectMock = $this->getMockBuilder(CurrentPageObject::class)
            ->setMethods(['getCurrentProductIdForBlock', 'getCurrentCategoryIdForBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepository::class);

        $this->ruleTypeResolverMock = $this->getMockBuilder(TypeResolver::class)
            ->setMethods(['isRuleTypeUseCategoryRelatedProductCondition'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepositoryMock = $this->getMockForAbstractClass(
            CategoryRepositoryInterface::class
        );

        $this->validator = $objectManager->getObject(
            Validator::class,
            [
                'productVisibility' => $this->productVisibilityMock,
                'productCollection' => $this->productCollectionMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'currentPageObject' => $this->currentPageObjectMock,
                'storeManager' => $this->storeManagerMock,
                'productRepository' => $this->productRepositoryMock,
                'ruleTypeResolver' => $this->ruleTypeResolverMock,
                'categoryRepository' => $this->categoryRepositoryMock
            ]
        );
    }

    /**
     * Testing of validateAndGetProducts method for category block type
     */
    public function testValidateAndGetProductsForCategoryBlockType()
    {
        $type = SourceType::CATEGORY_BLOCK_TYPE;
        $relatedMatchingProductIds = [4, 2, 5, 7, 59, 6];
        $productMock1 = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock2 = $this->getMockForAbstractClass(ProductInterface::class);
        $products = [$productMock1, $productMock2];
        $relatedConditionType = ProductConditionType::CONDITIONS_COMBINATION;
        $quoteId = 1;
        $quoteProductId = 99;

        $relatedProductMock = $this->getMockBuilder(RelatedCategoryProduct::class)
            ->setMethods(['getMatchingProductIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $relatedProductMock->expects($this->once())
            ->method('getMatchingProductIds')
            ->willReturn($relatedMatchingProductIds);

        $ruleMock = $this->getMockBuilder(Rule::class)
            ->setMethods(
                [
                    'getType',
                    'getRelatedProductRule',
                    'getSortType',
                    'getLimit',
                    'getIsDisplayOutofstock',
                    'getProductConditionType'
                ]
            )->disableOriginalConstructor()
            ->getMock();
        $ruleMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $ruleMock->expects($this->once())
            ->method('getRelatedProductRule')
            ->willReturn($relatedProductMock);
        $ruleMock->expects($this->once())
            ->method('getIsDisplayOutofstock')
            ->willReturn(false);
        $ruleMock->expects($this->once())
            ->method('getProductConditionType')
            ->willReturn($relatedConditionType);

        $quoteItemMock = $this->getMockBuilder(QuoteItem::class)
            ->setMethods(['getProductId'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($quoteProductId);

        $quoteItemCollectionMock = $this->getMockBuilder(QuoteItemCollection::class)
            ->setMethods(['getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$quoteItemMock]));
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getId', 'getItemsCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $quoteMock->expects($this->once())
            ->method('getItemsCollection')
            ->willReturn($quoteItemCollectionMock);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $storeMock = $this->getMockBuilder(Store::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->productCollectionMock->expects($this->once())
            ->method('resetCollection')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setVisibility')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addInStockFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addIdFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addProductSorting')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($products);
        $this->productCollectionMock->expects($this->once())
            ->method('addUrlRewrite')
            ->willReturnSelf();

        $this->ruleTypeResolverMock->expects($this->once())
            ->method('isRuleTypeUseCategoryRelatedProductCondition')
            ->willReturn(true);

        $this->assertTrue(count($this->validator->validateAndGetProducts($ruleMock, $type)) > 0);
    }

    /**
     * Testing of validateAndGetProducts method for product and cart block type
     */
    public function testValidateAndGetProductsForProductAndCartBlockType()
    {
        $type = SourceType::PRODUCT_BLOCK_TYPE;
        $relatedMatchingProductIds = [4, 2, 5, 7, 59, 6];
        $productMock1 = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock2 = $this->getMockForAbstractClass(ProductInterface::class);
        $products = [$productMock1, $productMock2];
        $currentProductId = 1;
        $quoteId = 1;
        $quoteProductId = 4;
        $currentCategoryId = 5;
        $relatedConditionType = ProductConditionType::CONDITIONS_COMBINATION;

        $this->currentPageObjectMock->expects($this->exactly(2))
            ->method('getCurrentProductIdForBlock')
            ->willReturn($currentProductId);

        $this->currentPageObjectMock->expects($this->once())
            ->method('getCurrentCategoryIdForBlock')
            ->willReturn($currentCategoryId);

        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getAwArpOverrideNative'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getAwArpOverrideNative')
            ->willReturn(false);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($currentProductId)
            ->willReturn($productMock);

        $relatedProductMock = $this->getMockBuilder(RelatedProduct::class)
            ->setMethods(['getMatchingProductIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $relatedProductMock->expects($this->once())
            ->method('getMatchingProductIds')
            ->willReturn($relatedMatchingProductIds);

        $ruleMock = $this->getMockBuilder(Rule::class)
            ->setMethods(
                [
                    'getType',
                    'getRelatedProductRule',
                    'getSortType',
                    'getLimit',
                    'getIsDisplayOutofstock',
                    'getProductConditionType'
                ]
            )->disableOriginalConstructor()
            ->getMock();
        $ruleMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $ruleMock->expects($this->once())
            ->method('getRelatedProductRule')
            ->willReturn($relatedProductMock);
        $ruleMock->expects($this->once())
            ->method('getIsDisplayOutofstock')
            ->willReturn(false);
        $ruleMock->expects($this->once())
            ->method('getProductConditionType')
            ->willReturn($relatedConditionType);

        $quoteItemMock = $this->getMockBuilder(QuoteItem::class)
            ->setMethods(['getProductId'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($quoteProductId);

        $quoteItemCollectionMock = $this->getMockBuilder(QuoteItemCollection::class)
            ->setMethods(['getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$quoteItemMock]));
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getId', 'getItemsCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $quoteMock->expects($this->once())
            ->method('getItemsCollection')
            ->willReturn($quoteItemCollectionMock);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $storeMock = $this->getMockBuilder(Store::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->productCollectionMock->expects($this->once())
            ->method('resetCollection')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setVisibility')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addInStockFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addIdFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addProductSorting')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($products);

        $this->ruleTypeResolverMock->expects($this->once())
            ->method('isRuleTypeUseCategoryRelatedProductCondition')
            ->willReturn(false);

        $this->assertTrue(count($this->validator->validateAndGetProducts($ruleMock, $type)) > 0);
    }

    /**
     * Testing of validateAndGetProducts method for native related products
     */
    public function testValidateAndGetProductsForNativeRelated()
    {
        $type = SourceType::PRODUCT_BLOCK_TYPE;
        $relatedProductIds = [4, 2, 59, 6];
        $productMock1 = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock2 = $this->getMockForAbstractClass(ProductInterface::class);
        $products = [$productMock1, $productMock2];
        $currentProductId = 1;
        $currentCategoryId = 5;
        $relatedConditionType = ProductConditionType::CONDITIONS_COMBINATION;
        $quoteId = 1;
        $quoteProductId = 4;

        $this->currentPageObjectMock->expects($this->exactly(2))
            ->method('getCurrentProductIdForBlock')
            ->willReturn($currentProductId);
        $this->currentPageObjectMock->expects($this->once())
            ->method('getCurrentCategoryIdForBlock')
            ->willReturn($currentCategoryId);

        $linkProductCollectionMock = $this->getMockBuilder(LinkProductCollection::class)
            ->setMethods(
                [
                    'addAttributeToSelect',
                    'setPositionOrder',
                    'addStoreFilter',
                    'load',
                    'getAllIds'
                ]
            )->disableOriginalConstructor()
            ->getMock();
        $linkProductCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->willReturnSelf();
        $linkProductCollectionMock->expects($this->once())
            ->method('setPositionOrder')
            ->willReturnSelf();
        $linkProductCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->willReturnSelf();
        $linkProductCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $linkProductCollectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($relatedProductIds);

        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getAwArpOverrideNative', 'getRelatedProductCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getAwArpOverrideNative')
            ->willReturn(true);
        $productMock->expects($this->once())
            ->method('getRelatedProductCollection')
            ->willReturn($linkProductCollectionMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($currentProductId)
            ->willReturn($productMock);

        $ruleMock = $this->getMockBuilder(Rule::class)
            ->setMethods(
                [
                    'getType',
                    'getIsDisplayOutofstock',
                    'getProductConditionType'
                ]
            )->disableOriginalConstructor()
            ->getMock();
        $ruleMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $ruleMock->expects($this->once())
            ->method('getIsDisplayOutofstock')
            ->willReturn(false);
        $ruleMock->expects($this->once())
            ->method('getProductConditionType')
            ->willReturn($relatedConditionType);

        $storeMock = $this->getMockBuilder(Store::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->productCollectionMock->expects($this->once())
            ->method('resetCollection')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setVisibility')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addInStockFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addIdFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addProductSorting')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($products);

        $quoteItemMock = $this->getMockBuilder(QuoteItem::class)
            ->setMethods(['getProductId'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($quoteProductId);

        $quoteItemCollectionMock = $this->getMockBuilder(QuoteItemCollection::class)
            ->setMethods(['getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$quoteItemMock]));
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getId', 'getItemsCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $quoteMock->expects($this->once())
            ->method('getItemsCollection')
            ->willReturn($quoteItemCollectionMock);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $this->assertTrue(count($this->validator->validateAndGetProducts($ruleMock, $type)) > 0);
    }
}
