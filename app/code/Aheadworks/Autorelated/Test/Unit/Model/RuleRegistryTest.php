<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\RuleRegistry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\RuleRegistry
 */
class RuleRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RuleRegistry
     */
    private $ruleRegistry;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->ruleRegistry = $objectManager->getObject(
            RuleRegistry::class,
            []
        );
    }

    /**
     * Testing of retrieve method on null
     */
    public function testRetrieveNull()
    {
        $ruleId = 1;

        $this->assertNull($this->ruleRegistry->retrieve($ruleId));
    }

    /**
     * Testing of retrieve method on object
     */
    public function testRetrieveObject()
    {
        $ruleId = 1;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $this->ruleRegistry->push($ruleMock);
        $this->assertEquals($ruleMock, $this->ruleRegistry->retrieve($ruleId));
    }

    /**
     * Testing of remove method
     */
    public function testRemove()
    {
        $ruleId = 1;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $this->ruleRegistry->push($ruleMock);

        $ruleFromReg = $this->ruleRegistry->retrieve($ruleId);
        $this->assertEquals($ruleMock, $ruleFromReg);
        $this->ruleRegistry->remove($ruleId);
        $this->assertNull($this->ruleRegistry->retrieve($ruleId));
    }
}
