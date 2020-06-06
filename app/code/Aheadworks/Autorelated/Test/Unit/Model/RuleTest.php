<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Rule\Related\ProductFactory as RelatedProductFactory;
use Aheadworks\Autorelated\Model\Rule\Related\Product as RelatedProduct;
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProductFactory as RelatedCategoryProductFactory;
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProduct as RelatedCategoryProduct;
use Aheadworks\Autorelated\Model\Rule\Viewed\ProductFactory as ViewedProductFactory;
use Aheadworks\Autorelated\Model\Rule\Viewed\Product as ViewedProduct;
use Aheadworks\Autorelated\Model\ResourceModel\Validator\CodeIsUnique as CodeIsUniqueValidator;
use Aheadworks\Autorelated\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Aheadworks\Autorelated\Model\Rule;
use Aheadworks\Autorelated\Model\Serialize\SerializeInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Api\Data\ConditionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\Serialize\Factory as SerializeFactory;

/**
 * Test for \Aheadworks\Autorelated\Model\Rule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Rule
     */
    private $rule;

    /**
     * RelatedProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $relatedProductFactoryMock;

    /**
     * RelatedCategoryProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $relatedCategoryProductFactoryMock;

    /**
     * ViewedProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewedProductFactoryMock;

    /**
     * CodeIsUniqueValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeIsUniqueValidatorMock;

    /**
     * ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * TypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleTypeResolverMock;

    /**
     * @var SerializeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    /**
     * @var array
     */
    private $ruleData = [
        'id' => '1',
        'type' => Type::PRODUCT_BLOCK_TYPE,
        'position' => Position::PRODUCT_CONTENT_TOP,
        'viewed_condition' => [
            'type' => \Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine::class,
            'aggregator' => 'all',
            'value' => '1',
            'value_type' => null
        ],
        'product_condition' => [
            'type' => \Aheadworks\Autorelated\Model\Rule\Related\Condition\Combine::class,
            'aggregator' => 'all',
            'value' => '1',
            'value_type' => null
        ],
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->relatedProductFactoryMock = $this->getMockBuilder(RelatedProductFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->relatedCategoryProductFactoryMock = $this->getMockBuilder(RelatedCategoryProductFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewedProductFactoryMock = $this->getMockBuilder(ViewedProductFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->codeIsUniqueValidatorMock = $this->getMockBuilder(CodeIsUniqueValidator::class)
            ->setMethods(['validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->conditionConverterMock = $this->getMockBuilder(ConditionConverter::class)
            ->setMethods(['dataModelToArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleTypeResolverMock = $this->getMockBuilder(TypeResolver::class)
            ->setMethods(['getType', 'isRuleTypeUseCategoryRelatedProductCondition'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializerMock = $this->getMockForAbstractClass(SerializeInterface::class);
        $serializeFactoryMock = $this->createPartialMock(SerializeFactory::class, ['create']);
        $serializeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->serializerMock);

        $this->rule = $objectManager->getObject(
            Rule::class,
            [
                'relatedProductFactory' => $this->relatedProductFactoryMock,
                'relatedCategoryProductFactory' => $this->relatedCategoryProductFactoryMock,
                'viewedProductFactory' => $this->viewedProductFactoryMock,
                'codeIsUniqueValidator' => $this->codeIsUniqueValidatorMock,
                'conditionConverter' => $this->conditionConverterMock,
                'ruleTypeResolver' => $this->ruleTypeResolverMock,
                'serializeFactory' => $serializeFactoryMock
            ]
        );
    }

    /**
     * Testing of getRelatedProductRule method for product type block
     */
    public function testGetRelatedProductRuleForProductType()
    {
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $relatedConditionArray = $this->ruleData['product_condition'];
        $this->ruleData['product_condition'] = $conditionMock;
        $this->rule->setData($this->ruleData);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($relatedConditionArray);

        $relatedProductMock = $this->getMockBuilder(RelatedProduct::class)
            ->setMethods(['setConditions', 'getConditions', 'loadArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $relatedProductMock->expects($this->once())
            ->method('setConditions')
            ->willReturnSelf();
        $relatedProductMock->expects($this->once())
            ->method('getConditions')
            ->willReturnSelf();
        $relatedProductMock->expects($this->once())
            ->method('loadArray')
            ->willReturnSelf();
        $this->relatedProductFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($relatedProductMock);

        $this->ruleTypeResolverMock->expects($this->once())
            ->method('isRuleTypeUseCategoryRelatedProductCondition')
            ->willReturn(false);

        $this->rule->getRelatedProductRule();
    }

    /**
     * Testing of getRelatedProductRule method for category type block
     */
    public function testGetRelatedProductRuleForCategoryType()
    {
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $relatedConditionArray = $this->ruleData['product_condition'];
        $this->ruleData['product_condition'] = $conditionMock;
        $this->ruleData['type'] = Type::CATEGORY_BLOCK_TYPE;
        $this->ruleData['position'] = Position::CATEGORY_CONTENT_BOTTOM;
        $this->rule->setData($this->ruleData);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($relatedConditionArray);

        $relatedProductMock = $this->getMockBuilder(RelatedCategoryProduct::class)
            ->setMethods(['setConditions', 'getConditions', 'loadArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $relatedProductMock->expects($this->once())
            ->method('setConditions')
            ->willReturnSelf();
        $relatedProductMock->expects($this->once())
            ->method('getConditions')
            ->willReturnSelf();
        $relatedProductMock->expects($this->once())
            ->method('loadArray')
            ->willReturnSelf();
        $this->relatedCategoryProductFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($relatedProductMock);

        $this->ruleTypeResolverMock->expects($this->once())
            ->method('isRuleTypeUseCategoryRelatedProductCondition')
            ->willReturn(true);

        $this->rule->getRelatedProductRule();
    }

    /**
     * Testing of getViewedProductRule method
     */
    public function testGetViewedProductRule()
    {
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $viewedConditionArray = $this->ruleData['viewed_condition'];
        $this->ruleData['viewed_condition'] = $conditionMock;
        $this->rule->setData($this->ruleData);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($viewedConditionArray);

        $viewedProductMock = $this->getMockBuilder(ViewedProduct::class)
            ->setMethods(['setConditions', 'getConditions', 'loadArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $viewedProductMock->expects($this->once())
            ->method('setConditions')
            ->willReturnSelf();
        $viewedProductMock->expects($this->once())
            ->method('getConditions')
            ->willReturnSelf();
        $viewedProductMock->expects($this->once())
            ->method('loadArray')
            ->willReturnSelf();
        $this->viewedProductFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($viewedProductMock);

        $this->rule->getViewedProductRule();
    }

    /**
     * Testing of beforeSave method
     */
    public function testBeforeSave()
    {
        $serializedValue = 'serialized';
        $this->rule->setData($this->ruleData);
        $this->serializerMock->expects($this->exactly(2))
            ->method('serialize')
            ->withConsecutive(
                [$this->ruleData[RuleInterface::VIEWED_CONDITION]],
                [$this->ruleData[RuleInterface::PRODUCT_CONDITION]]
            )
            ->willReturnOnConsecutiveCalls($serializedValue, $serializedValue);

        $this->ruleTypeResolverMock->expects($this->once())
            ->method('getType')
            ->with(Position::PRODUCT_CONTENT_TOP)
            ->willReturn(Type::PRODUCT_BLOCK_TYPE);
        $this->codeIsUniqueValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->rule)
            ->willReturn(true);

        $this->rule->beforeSave();
    }

    /**
     * Testing of validateBeforeSave method, that proper exception is thrown if rule with code exist
     *
     * @expectedException \Magento\Framework\Exception\AlreadyExistsException
     * @expectedExceptionMessage Rule name should be unique
     */
    public function testValidateBeforeSave()
    {
        $this->rule->setData($this->ruleData);
        $this->codeIsUniqueValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->rule)
            ->willReturn(false);
        $this->rule->validateBeforeSave();
    }
}
