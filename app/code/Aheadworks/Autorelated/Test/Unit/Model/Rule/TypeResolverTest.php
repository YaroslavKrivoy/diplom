<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Rule;

use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Type as SourceType;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Rule\TypeResolver
 */
class TypeResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var Position|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rulePositionSourceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->rulePositionSourceMock = $this->getMockForAbstractClass(Position::class);
        $this->typeResolver = $objectManager->getObject(
            TypeResolver::class,
            ['rulePositionSource' => $this->rulePositionSourceMock]
        );
    }

    /**
     * Testing of getType method
     *
     * @param int $type
     * @param int $position
     * @dataProvider getTypeDataProvider
     */
    public function testGetType($type, $position)
    {
        $this->assertEquals($type, $this->typeResolver->getType($position));
    }

    /**
     * Data provider for testGetType method
     *
     * @return array
     */
    public function getTypeDataProvider()
    {
        return [
            [SourceType::CATEGORY_BLOCK_TYPE, Position::CATEGORY_CONTENT_TOP],
            [SourceType::PRODUCT_BLOCK_TYPE, Position::PRODUCT_CONTENT_TOP],
            [SourceType::CART_BLOCK_TYPE, Position::CART_CONTENT_BOTTOM]
        ];
    }

    /**
     * Testing of isRuleTypeUseCategoryRelatedProductCondition method
     *
     * @param bool $result
     * @param int $type
     * @dataProvider isRuleTypeUseCategoryRelatedProductConditionDataProvider
     */
    public function testIsRuleTypeUseCategoryRelatedProductCondition($result, $type)
    {
        $this->assertEquals($result, $this->typeResolver->isRuleTypeUseCategoryRelatedProductCondition($type));
    }

    /**
     * Data provider for isRuleTypeUseCategoryRelatedProductCondition method
     *
     * @return array
     */
    public function isRuleTypeUseCategoryRelatedProductConditionDataProvider()
    {
        return [
            [true,  SourceType::CATEGORY_BLOCK_TYPE],
            [false, SourceType::PRODUCT_BLOCK_TYPE],
            [false, SourceType::CART_BLOCK_TYPE],
            [true,  SourceType::CUSTOM_BLOCK_TYPE]
        ];
    }

    /**
     * Testing of isRuleTypeUseCategoryRelatedProductCondition method
     *
     * @param bool $result
     * @param int $position
     * @dataProvider isRulePositionUseCategoryRelatedProductConditionDataProvider
     */
    public function testIsRulePositionUseCategoryRelatedProductCondition($result, $position)
    {
        $this->assertEquals($result, $this->typeResolver->isRulePositionUseCategoryRelatedProductCondition($position));
    }

    /**
     * Data provider for isRuleTypeUseCategoryRelatedProductCondition method
     *
     * @return array
     */
    public function isRulePositionUseCategoryRelatedProductConditionDataProvider()
    {
        return [
            [true,  Position::CATEGORY_CONTENT_TOP],
            [false, Position::PRODUCT_CONTENT_TOP],
            [false, Position::CART_CONTENT_BOTTOM],
            [true,  Position::CUSTOM]
        ];
    }
}
