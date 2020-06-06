<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Aheadworks\Autorelated\Controller\Adminhtml\Rule\NewAction;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Controller\Adminhtml\Rule\NewAction
 */
class NewActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NewAction
     */
    private $newAction;

    /**
     * @var ForwardFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $forwardFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->forwardFactoryMock = $this->getMockBuilder(ForwardFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            []
        );

        $this->newAction = $objectManager->getObject(
            NewAction::class,
            [
                'context' => $contextMock,
                'resultForwardFactory' => $this->forwardFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $resultForwardMock = $this->getMockBuilder(Forward::class)
            ->setMethods(['forward'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultForwardMock->expects($this->once())
            ->method('forward')
            ->willReturnSelf();
        $this->forwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultForwardMock);

        $this->assertSame($resultForwardMock, $this->newAction->execute());
    }
}
