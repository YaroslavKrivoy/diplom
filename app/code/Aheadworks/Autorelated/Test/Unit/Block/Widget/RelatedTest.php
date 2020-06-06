<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Block\Widget;

use Aheadworks\Autorelated\Block\Widget\Related;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Api\BlockRepositoryInterface;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\Pricing\Render as PricingRender;

/**
 * Test for \Aheadworks\Autorelated\Block\Widget\Related
 *
 */
class RelatedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Related
     */
    private $related;

    /**
     * @var BlockRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blocksRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blocksRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->related = $objectManager->getObject(
            Related::class,
            [
                'blocksRepository' => $this->blocksRepositoryMock
            ]
        );
    }

    /**
     * Testing of getBlocks method
     */
    public function testGetBlocks()
    {
        $ruleId = 1;

        $this->related->setData('rule_id', $ruleId);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock->expects($this->once())
            ->method('getRule')
            ->willReturn($ruleMock);
        $blockSearchResultsMock = $this->getMockForAbstractClass(BlockSearchResultsInterface::class);
        $blockSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$blockMock]);
        $this->blocksRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($blockSearchResultsMock);

        $this->assertSame([$blockMock], $this->related->getBlocks());
    }

    /**
     * Testing of getNameInLayout method
     */
    public function testGetNameInLayout()
    {
        $ruleId = 1;
        $expected = Related::WIDGET_NAME_PREFIX . $ruleId;

        $this->related->setData('rule_id', $ruleId);
        $this->assertEquals($expected, $this->related->getNameInLayout());
    }
}
