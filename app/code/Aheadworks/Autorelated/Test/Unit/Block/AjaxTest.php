<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Block;

use Aheadworks\Autorelated\Block\Ajax;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout\ProcessorInterface;

/**
 * Test for \Aheadworks\Autorelated\Block\Ajax
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Ajax
     */
    private $block;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['isSecure']
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );

        $this->block = $objectManager->getObject(
            Ajax::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of getScriptOptions method
     */
    public function testGetScriptOptions()
    {
        $isSecure = false;

        $url = 'https://ecommerce.aheadworks.com/autorelated/view/process/id/1369/';
        $expected = '{"url":"https:\/\/ecommerce.aheadworks.com\/autorelated\/view\/process\/id\/1369\/"}';

        $this->requestMock->expects($this->once())
            ->method('isSecure')
            ->willReturn($isSecure);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'autorelated/view/process/',
                [
                    '_current' => true,
                    '_secure' => $isSecure,
                ]
            )->willReturn($url);

        $this->assertEquals($expected, $this->block->getScriptOptions());
    }
}
