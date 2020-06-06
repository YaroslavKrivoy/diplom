<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Observer;

use Aheadworks\Autorelated\Observer\CheckRelatedProduct;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Autorelated\Api\StatisticManagerInterface;

/**
 * Test for \Aheadworks\Autorelated\Observer\CheckRelatedProduct
 */
class CheckRelatedProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CheckRelatedProduct
     */
    private $checkRelatedProduct;

    /**
     * @var StatisticManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticManagerMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $observerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->statisticManagerMock = $this->getMockForAbstractClass(StatisticManagerInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->checkRelatedProduct = $objectManager->getObject(
            CheckRelatedProduct::class,
            [
                'statisticManager' => $this->statisticManagerMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $ruleId = 1;

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('awarp_rule'))
            ->willReturn($ruleId);

        $this->statisticManagerMock->expects($this->once())
            ->method('updateRuleClicks')
            ->with($ruleId)
            ->willReturn(true);

        $this->checkRelatedProduct->execute($this->observerMock);
    }
}
