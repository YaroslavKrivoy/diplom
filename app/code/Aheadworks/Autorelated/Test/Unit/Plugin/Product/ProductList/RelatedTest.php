<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Test\Unit\Plugin\Product\ProductList;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\BlockReplacementManager;
use Aheadworks\Autorelated\Plugin\Block\Product\ProductList\Related as RelatedPlugin;
use Magento\Catalog\Block\Product\ProductList\Related as RelatedBlock;

/**
 * Test for \Aheadworks\Autorelated\Plugin\Block\Product\ProductList\Related
 */
class RelatedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RelatedPlugin
     */
    private $relatedPlugin;

    /**
     * @var BlockReplacementManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockReplacementManager;

    /**
     * @var RelatedBlock|\PHPUnit_Framework_MockObject_MockObject
     */
    private $relatedBlockMock;

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @var string
     */
    private $testHtmlContent;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->testHtmlContent = "Test HTML Content";

        $this->blockReplacementManager = $this->getMockBuilder(BlockReplacementManager::class)
            ->setMethods(['getIsArpUsedInsteadFlag'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->relatedBlockMock = $this->getMockBuilder(RelatedBlock::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->closure = function () {
            return $this->testHtmlContent;
        };

        $this->relatedPlugin = $objectManager->getObject(
            RelatedPlugin::class,
            [
                'blockReplacementManager' => $this->blockReplacementManager
            ]
        );
    }

    /**
     * Testing of aroundToHtml method with flag set to true
     */
    public function testAroundToHtmlReturnEmptyBlock()
    {
        $this->blockReplacementManager->expects($this->once())
            ->method('getIsArpUsedInsteadFlag')
            ->with($this->relatedBlockMock)
            ->willReturn(true);

        $this->assertEquals(
            RelatedPlugin::EMPTY_BLOCK_CONTENT,
            $this->relatedPlugin->aroundToHtml($this->relatedBlockMock, $this->closure, [])
        );
    }

    /**
     * Testing of aroundToHtml method with flag unset
     */
    public function testAroundToHtmlReturnNativeBlockContent()
    {
        $this->blockReplacementManager->expects($this->once())
            ->method('getIsArpUsedInsteadFlag')
            ->with($this->relatedBlockMock)
            ->willReturn(false);

        $this->assertEquals(
            $this->testHtmlContent,
            $this->relatedPlugin->aroundToHtml($this->relatedBlockMock, $this->closure, [])
        );
    }
}
