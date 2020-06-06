<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\ResourceModel;

use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Api\Data\BlockInterfaceFactory;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterfaceFactory;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Autorelated\Model\Rule\Related\Validator as RelatedValidator;
use Aheadworks\Autorelated\Model\Rule\Viewed\Validator as ViewedValidator;
use Magento\Customer\Model\Session;
use Aheadworks\Autorelated\Model\ResourceModel\BlockRepository;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Template;
use Aheadworks\Autorelated\Model\Source\Sort;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use \Magento\Customer\Model\Context as CustomerContext;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Test \Aheadworks\Autorelated\Model\ResourceModel\BlockRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var SortOrderBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * @var RelatedValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $relatedValidatorMock;

    /**
     * @var ViewedValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewedValidatorMock;

    /**
     * @var BlockSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var BlockInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockFactoryMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var array
     */
    private $ruleData = [
        'id' => 1,
        'type' => Type::PRODUCT_BLOCK_TYPE,
        'position' => Position::PRODUCT_CONTENT_TOP,
        'click_count' => 2,
        'view_count' => 4
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->httpContextMock = $this->getMockBuilder(HttpContext::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'addSortOrder', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->setMethods(['setField', 'setDirection', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->relatedValidatorMock = $this->getMockBuilder(RelatedValidator::class)
            ->setMethods(['validateAndGetProducts'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewedValidatorMock = $this->getMockBuilder(ViewedValidator::class)
            ->setMethods(['canShow'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(BlockSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->blockFactoryMock = $this->getMockBuilder(BlockInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isEnterpriseCustomerSegmentEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockRepository = $objectManager->getObject(
            BlockRepository::class,
            [
                'httpContext' => $this->httpContextMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'sortOrderBuilder' => $this->sortOrderBuilderMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'storeManager' => $this->storeManagerMock,
                'relatedValidator' => $this->relatedValidatorMock,
                'viewedValidator' => $this->viewedValidatorMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'blockFactory' => $this->blockFactoryMock,
                'config' => $this->configMock
            ]
        );
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $customerGroupId = 1;
        $storeId = 1;
        $blockPosition = $this->ruleData['position'];
        $blockType = $this->ruleData['type'];
        $productMock1 = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock2 = $this->getMockForAbstractClass(ProductInterface::class);
        $validateAndGetProducts = [$productMock1, $productMock2];

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->exactly(5))
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->httpContextMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap(
                [
                    [CustomerContext::CONTEXT_GROUP, null, $customerGroupId],
                    [StoreManagerInterface::CONTEXT_STORE, null, $storeId]
                ]
            );
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleSearchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        $ruleSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ruleMock]);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($ruleSearchResultsMock);

        $this->viewedValidatorMock->expects($this->once())
            ->method('canShow')
            ->willReturn(true);
        $this->relatedValidatorMock->expects($this->once())
            ->method('validateAndGetProducts')
            ->willReturn($validateAndGetProducts);

        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock->expects($this->once())
            ->method('setRule')
            ->with($ruleMock);
        $blockMock->expects($this->once())
            ->method('setProducts')
            ->with($validateAndGetProducts);
        $this->blockFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($blockMock);

        $searchResultsMock = $this->getMockForAbstractClass(BlockSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$blockMock])
            ->willReturnSelf();
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with(1)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $this->configMock->expects($this->once())
            ->method('isEnterpriseCustomerSegmentEnabled')
            ->willReturn(false);

        $this->assertSame($searchResultsMock, $this->blockRepository->getList($blockType, $blockPosition));
    }
}
