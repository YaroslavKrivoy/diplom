<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Test\Unit\Observer\Order\Email;

use Aheadworks\OneStepCheckout\Observer\Order\Email\VariableObserver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event\Observer;
use Aheadworks\OneStepCheckout\Model\Order\Email\VariableProcessor as OrderVariableProcessor;
use Magento\Sales\Model\Order;
use Magento\Framework\DataObject;

/**
 * Class VariableObserverTest
 *
 * @package Aheadworks\OneStepCheckout\Test\Unit\Observer\Order\Email
 */
class VariableObserverTest extends TestCase
{
    /**
     * @var VariableObserver
     */
    private $observer;

    /**
     * @var OrderVariableProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $variableProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->variableProcessorMock = $this->createMock(
            OrderVariableProcessor::class
        );
        $this->observer = $objectManager->getObject(
            VariableObserver::class,
            [
                'variableProcessor' => $this->variableProcessorMock
            ]
        );
    }

    /**
     * Test execute method
     *
     * @param Observer $observerMock
     * @param bool $isProcessorCalled
     * @param Order $orderMock
     * @dataProvider executeDataProvider
     */
    public function testExecute($observerMock, $isProcessorCalled, $orderMock)
    {
        if ($isProcessorCalled) {
            $this->variableProcessorMock->expects($this->once())
                ->method('addDeliveryDateVariables')
                ->with($orderMock)
                ->willReturn(null);
        } else {
            $this->variableProcessorMock->expects($this->never())
                ->method('addDeliveryDateVariables');
        }

        $this->observer->execute($observerMock);
    }

    /**
     * Data provider for execute
     *
     * @return array
     */
    public function executeDataProvider()
    {
        $orderMock = $this->createMock(Order::class);
        $transportObjectMock = $this->createPartialMock(DataObject::class, ['getOrder']);
        $transportObjectMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);
        $transportMock = $this->createPartialMock(DataObject::class, ['getOrder']);
        $transportMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);
        $transportObjectMockNoOrder = $this->createPartialMock(DataObject::class, ['getOrder']);
        $transportObjectMockNoOrder->expects($this->any())
            ->method('getOrder')
            ->willReturn(null);
        $transportMockNoOrder = $this->createPartialMock(DataObject::class, ['getOrder']);
        $transportMockNoOrder->expects($this->any())
            ->method('getOrder')
            ->willReturn(null);
        return [
            [
                $this->getObserverMock($transportObjectMock, $transportMock),
                true,
                $orderMock
            ],
            [
                $this->getObserverMock($transportObjectMock, null),
                true,
                $orderMock
            ],
            [
                $this->getObserverMock($transportObjectMock, $transportMockNoOrder),
                true,
                $orderMock
            ],
            [
                $this->getObserverMock(null, $transportMock),
                true,
                $orderMock
            ],
            [
                $this->getObserverMock($transportObjectMockNoOrder, $transportMock),
                true,
                $orderMock
            ],
            [
                $this->getObserverMock(null, null),
                false,
                $orderMock
            ],
            [
                $this->getObserverMock($transportObjectMockNoOrder, $transportMockNoOrder),
                false,
                $orderMock
            ],
            [
                $this->getObserverMock(null, $transportMockNoOrder),
                false,
                $orderMock
            ],
            [
                $this->getObserverMock($transportObjectMockNoOrder, null),
                false,
                $orderMock
            ],
        ];
    }

    /**
     * Retrieve observer mock
     *
     * @param \PHPUnit\Framework\MockObject\MockObject|DataObject|null $transportObject
     * @param \PHPUnit\Framework\MockObject\MockObject|DataObject|null $transport
     * @return \PHPUnit\Framework\MockObject\MockObject|Observer
     */
    private function getObserverMock($transportObject, $transport)
    {
        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->any())
            ->method('getData')
            ->willReturnMap(
                [
                    [
                        'transportObject',
                        null,
                        $transportObject
                    ],
                    [
                        'transport',
                        null,
                        $transport
                    ],
                ]
            );
        return $observerMock;
    }
}
