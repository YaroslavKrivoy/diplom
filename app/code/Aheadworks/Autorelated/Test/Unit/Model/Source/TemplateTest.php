<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Source;

use Aheadworks\Autorelated\Model\Source\Template;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Source\Template
 */
class TemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Template
     */
    private $template;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->template = $objectManager->getObject(
            Template::class,
            []
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->template->toOptionArray()));
    }
}
