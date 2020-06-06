<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\ResourceModel;

use Aheadworks\Autorelated\Api\Data\ConditionInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Template;
use Aheadworks\Autorelated\Model\Source\Sort;
use Aheadworks\Autorelated\Model\Rule;
use Aheadworks\Autorelated\Model\Data\Rule as DataRule;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Autorelated\Model\RuleFactory;
use Aheadworks\Autorelated\Api\Data\RuleInterfaceFactory;
use Aheadworks\Autorelated\Model\RuleRegistry;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Autorelated\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\ResourceModel\RuleRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Autorelated\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\Serialize\Factory as SerializeFactory;
use Aheadworks\Autorelated\Model\Serialize\SerializeInterface;

/**
 * Test \Aheadworks\Autorelated\Model\ResourceModel\RuleRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleFactoryMock;

    /**
     * @var RuleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleDataFactoryMock;

    /**
     * @var RuleRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRegistryMock;

    /**
     * @var RuleSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * @var SerializeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    /**
     * @var array
     */
    private $ruleData = [
        'id' => 1,
        'viewed_condition' => 'a:4:{s:4:"type";s:58:"Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine"'
            . ';s:10:"aggregator";s:3:"all";s:5:"value";s:1:"1";s:10:"value_type";N;}',
        'product_condition' => 'a:4:{s:4:"type";s:59:"Aheadworks\Autorelated\Model\Rule\Related\Condition\Combine"'
            . ';s:10:"aggregator";s:3:"all";s:5:"value";s:1:"1";s:10:"value_type";N;}',
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['load', 'delete', 'save'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleFactoryMock = $this->getMockBuilder(RuleFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleDataFactoryMock = $this->getMockBuilder(RuleInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleRegistryMock = $this->getMockBuilder(RuleRegistry::class)
            ->setMethods(['push', 'retrieve', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(RuleSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->conditionConverterMock = $this->getMockBuilder(ConditionConverter::class)
            ->setMethods(['arrayToDataModel'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializerMock = $this->getMockForAbstractClass(SerializeInterface::class);
        $serializeFactoryMock = $this->createPartialMock(SerializeFactory::class, ['create']);
        $serializeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->serializerMock);

        $this->ruleRepository = $objectManager->getObject(
            RuleRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'ruleFactory' => $this->ruleFactoryMock,
                'ruleDataFactory' => $this->ruleDataFactoryMock,
                'ruleRegistry' => $this->ruleRegistryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'conditionConverter' => $this->conditionConverterMock,
                'serializeFactory' => $serializeFactoryMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        $unserializedValue = [];

        $this->dataObjectProcessorMock->expects($this->exactly(2))
            ->method('buildOutputDataArray')
            ->willReturn($this->ruleData);
        $ruleModelMock = $this->getMockBuilder(Rule::class)
            ->setMethods(['addData', 'beforeSave', 'getViewedCondition', 'getProductCondition'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleModelMock);
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($ruleModelMock, $this->ruleData['id']);
        $ruleModelMock->expects($this->once())
            ->method('addData')
            ->with($this->ruleData);
        $ruleModelMock->expects($this->exactly(2))
            ->method('getViewedCondition')
            ->willReturn($this->ruleData['viewed_condition']);
        $ruleModelMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->ruleData['product_condition']);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($ruleModelMock);

        $this->serializerMock->expects($this->exactly(2))
            ->method('unserialize')
            ->withConsecutive(
                [$this->ruleData[RuleInterface::VIEWED_CONDITION]],
                [$this->ruleData[RuleInterface::PRODUCT_CONDITION]]
            )
            ->willReturnOnConsecutiveCalls($unserializedValue, $unserializedValue);

        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->exactly(2))
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $dataRuleMock = $this->getMockBuilder(DataRule::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataRuleMock);
        $this->ruleRegistryMock->expects($this->once())
            ->method('push')
            ->with($dataRuleMock);

        $this->assertSame($dataRuleMock, $this->ruleRepository->save($ruleMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $unserializedValue = [];

        $dataRuleMock = $this->getMockBuilder(DataRule::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $dataRuleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);
        $dataRuleMock->expects($this->exactly(2))
            ->method('getViewedCondition')
            ->willReturn($this->ruleData['viewed_condition']);
        $dataRuleMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->ruleData['product_condition']);

        $this->ruleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataRuleMock);
        $this->ruleRegistryMock->expects($this->once())
            ->method('push')
            ->with($dataRuleMock);
        $this->ruleRegistryMock->expects($this->exactly(2))
            ->method('retrieve')
            ->with($this->ruleData['id'])
            ->will($this->onConsecutiveCalls(null, $dataRuleMock));

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($dataRuleMock, $this->ruleData['id']);

        $this->serializerMock->expects($this->exactly(2))
            ->method('unserialize')
            ->withConsecutive(
                [$this->ruleData[RuleInterface::VIEWED_CONDITION]],
                [$this->ruleData[RuleInterface::PRODUCT_CONDITION]]
            )
            ->willReturnOnConsecutiveCalls($unserializedValue, $unserializedValue);
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->exactly(2))
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $this->assertSame($dataRuleMock, $this->ruleRepository->get($this->ruleData['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if rule not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with ruleId = 1
     */
    public function testGetOnExeption()
    {
        $ruleId = 1;
        $dataRuleMock = $this->getMockBuilder(DataRule::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $dataRuleMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->ruleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataRuleMock);

        $this->assertSame($dataRuleMock, $this->ruleRepository->get($ruleId));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'Code';
        $filterValue = 'Test Rule';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;
        $unserializedValue = [];

        $searchCriteria = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collection = $this->getMockBuilder(RuleCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $ruleModelMock = $this->getMockBuilder(Rule::class)
            ->setMethods(['getCollection', 'getViewedCondition', 'getProductCondition'])
            ->disableOriginalConstructor()
            ->getMock();
        $ruleModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);
        $ruleModelMock->expects($this->exactly(2))
            ->method('getViewedCondition')
            ->willReturn($this->ruleData['viewed_condition']);
        $ruleModelMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->ruleData['product_condition']);

        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collection, RuleInterface::class);

        $filterGroup = $this->getMockBuilder(FilterGroup::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $filter = $this->getMockBuilder(Filter::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $filterGroup->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $filter->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filter->expects($this->exactly(5))
            ->method('getField')
            ->willReturn($filterName);
        $filter->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($filterValue);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with([$filterName], [['eq' => $filterValue]]);
        $collection
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $sortOrder = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteria->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrder]);
        $sortOrder->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $collection->expects($this->once())
            ->method('addOrder')
            ->with($filterName, SortOrder::SORT_ASC);
        $sortOrder->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_ASC);
        $searchCriteria->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($scCurrPage);
        $storeId = 0;
        $collection->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturn($collection);
        $collection->expects($this->once())
            ->method('setCurPage')
            ->with($scCurrPage)
            ->willReturn($collection);
        $searchCriteria->expects($this->once())
            ->method('getPageSize')
            ->willReturn($scPageSize);
        $collection->expects($this->once())
            ->method('setPageSize')
            ->with($scPageSize)
            ->willReturn($collection);
        $collection->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$ruleModelMock]));

        $this->serializerMock->expects($this->exactly(2))
            ->method('unserialize')
            ->withConsecutive(
                [$this->ruleData[RuleInterface::VIEWED_CONDITION]],
                [$this->ruleData[RuleInterface::PRODUCT_CONDITION]]
            )
            ->willReturnOnConsecutiveCalls($unserializedValue, $unserializedValue);
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->exactly(2))
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ruleModelMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->ruleRepository->getList($searchCriteria));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);
        $dataRuleMock = $this->getMockBuilder(DataRule::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $dataRuleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);

        $this->ruleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataRuleMock);
        $this->ruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($this->ruleData['id'])
            ->willReturn(null);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($dataRuleMock, $this->ruleData['id']);
        $this->ruleRegistryMock->expects($this->once())
            ->method('remove')
            ->with($this->ruleData['id']);

        $this->assertTrue($this->ruleRepository->delete($ruleMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $dataRuleMock = $this->getMockBuilder(DataRule::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $dataRuleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);

        $this->ruleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataRuleMock);
        $this->ruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($this->ruleData['id'])
            ->willReturn(null);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($dataRuleMock, $this->ruleData['id']);
        $this->ruleRegistryMock->expects($this->once())
            ->method('remove')
            ->with($this->ruleData['id']);

        $this->assertTrue($this->ruleRepository->deleteById($this->ruleData['id']));
    }
}
