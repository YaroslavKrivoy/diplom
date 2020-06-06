<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Block\Adminhtml\Rule\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button\Delete;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\App\Request\Http;

/**
 * Test for \Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button\Delete
 */
class DeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Delete
     */
    private $button;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockBuilder(Http::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );
        $this->button = $objectManager->getObject(
            Delete::class,
            [
                'context' => $contextMock,
                'ruleRepository' => $this->ruleRepositoryMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $ruleId = 1;
        $deleteUrl = 'https://ecommerce.aheadworks.com/index.php/admin/autorelated_admin/rule/delete/id/' . $ruleId;

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/delete'),
                $this->equalTo(['id' => $ruleId])
            )->willReturn($deleteUrl);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $this->ruleRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($ruleId)
            ->willReturn($ruleMock);

        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
