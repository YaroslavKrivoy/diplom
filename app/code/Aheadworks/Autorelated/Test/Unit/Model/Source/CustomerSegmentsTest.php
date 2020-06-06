<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Autorelated\Model\Config;
use Aheadworks\Autorelated\Model\Source\CustomerSegments;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Source\CustomerSegments
 */
class CustomerSegmentsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomerSegments
     */
    private $customerSegments;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setup()
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isEnterpriseCustomerSegmentInstalled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerSegments = $objectManager->getObject(
            CustomerSegments::class,
            [
                'config' => $this->configMock
            ]
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->configMock->expects($this->once())
            ->method('isEnterpriseCustomerSegmentInstalled')
            ->willReturn(false);

        $this->assertTrue(is_array($this->customerSegments->toOptionArray()));
    }
}
