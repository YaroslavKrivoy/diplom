<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Source;

use Aheadworks\Autorelated\Model\Source\Sort;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Source\Sort
 */
class SortTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Sort
     */
    private $sort;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->sort = $objectManager->getObject(
            Sort::class,
            []
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->sort->toOptionArray()));
    }
}
