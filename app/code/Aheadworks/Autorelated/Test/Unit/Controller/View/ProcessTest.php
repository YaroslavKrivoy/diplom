<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Controller\Block;

use Aheadworks\Autorelated\Controller\View\Process;
use Aheadworks\Autorelated\Api\StatisticManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

/**
 * Test for \Aheadworks\Autorelated\Controller\View\Process
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProcessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Process
     */
    private $controller;

    /**
     * @var StatisticManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticManagerMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->statisticManagerMock = $this->getMockForAbstractClass(
            StatisticManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['updateRuleViews']
        );

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            [
                'isAjax',
                'getParam'
            ]
        );
        $this->responseMock = $this->getMockForAbstractClass(
            ResponseInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['appendBody']
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'request' => $this->requestMock,
                'response' => $this->responseMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Process::class,
            [
                'context' => $contextMock,
                'statisticManager' => $this->statisticManagerMock
            ]
        );
    }

    /**
     * Testing of execute method, if is not ajax request
     */
    public function testExecuteIsNotAjax()
    {
        $resultRedirectMock = $this->getMockBuilder(ResultRedirect::class)
            ->setMethods(['setRefererOrBaseUrl'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method, if is ajax request
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteIsAjax()
    {
        $isAjax = true;
        $renderedRuleIds = [1, 2, 3];
        $processedRules = [1, 2, 3];

        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn($isAjax);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('blocks')
            ->willReturn(json_encode($renderedRuleIds));

        $this->statisticManagerMock->expects($this->exactly(count($renderedRuleIds)))
            ->method('updateRuleViews')
            ->willReturn(true);

        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with(json_encode($processedRules));

        $this->controller->execute();
    }
}
