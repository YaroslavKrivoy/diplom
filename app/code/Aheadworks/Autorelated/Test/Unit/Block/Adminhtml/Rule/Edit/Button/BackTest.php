<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Block\Adminhtml\Rule\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button\Back;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button\Back
 */
class BackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Back
     */
    private $button;
    
    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $backUrl = 'https://ecommerce.aheadworks.com/index.php/admin/autorelated_admin/rule';
        $urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/')
            ->willReturn($backUrl);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $urlBuilderMock
            ]
        );
        $this->button = $objectManager->getObject(
            Back::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
