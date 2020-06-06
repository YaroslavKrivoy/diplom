<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Source;

use Aheadworks\Autorelated\Model\Source\Position;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Source\Position
 */
class PositionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Position
     */
    private $position;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->position = $objectManager->getObject(
            Position::class,
            []
        );
    }

    /**
     * Testing of getOptionArray method
     */
    public function testGetOptionArray()
    {
        $this->assertTrue(is_array($this->position->getOptionArray()));
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->position->toOptionArray()));
    }

    /**
     * Testing of getProductPositions method
     */
    public function testGetProductPositions()
    {
        $this->assertTrue(is_array($this->position->getProductPositions()));
    }

    /**
     * Testing of getCartPositions method
     */
    public function testGetCartPositions()
    {
        $this->assertTrue(is_array($this->position->getCartPositions()));
    }

    /**
     * Testing of getCategoryPositions method
     */
    public function testGetCategoryPositions()
    {
        $this->assertTrue(is_array($this->position->getCategoryPositions()));
    }

    /**
     * Testing of getCustomPositions method
     */
    public function testGetCustomPositions()
    {
        $this->assertTrue(is_array($this->position->getCustomPositions()));
    }

    /**
     * Testing of getPositionLabel method
     *
     * @param string $expectedLabel
     * @param int $position
     * @dataProvider getPositionLabelDataProvider
     */
    public function testGetPositionLabel($expectedLabel, $position)
    {
        $this->assertEquals($expectedLabel, $this->position->getPositionLabel($position));
    }

    /**
     * @return array
     */
    public function getPositionLabelDataProvider()
    {
        return [
            ['Product page. Content top', Position::PRODUCT_CONTENT_TOP],
            ['Category page. Content top', Position::CATEGORY_CONTENT_TOP],
            ['Shopping cart page. Content top', Position::CART_CONTENT_TOP],
            ['Custom position', Position::CUSTOM]
        ];
    }
}
