<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Source;

use Aheadworks\Autorelated\Model\Source\Type;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Source\Template
 */
class TypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Type
     */
    private $type;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->type = $objectManager->getObject(
            Type::class,
            []
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->type->toOptionArray()));
    }
}
