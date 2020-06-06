<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\BlockReplacementManager;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\Autorelated\Model\BlockReplacementManager
 */
class BlockReplacementManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BlockReplacementManager
     */
    private $blockReplacementManager;

    /**
     * @var DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blockReplacementManager = $objectManager->getObject(BlockReplacementManager::class);

        $this->blockMock = $this->getMockBuilder(ObjectManager::class)
            ->setMethods(['getData', 'setData'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Testing of setIsArpUsedInsteadFlag method
     */
    public function testSetIsArpUsedInsteadFlag()
    {
        $this->blockMock->expects($this->once())
            ->method('setData')
            ->with(BlockReplacementManager::IS_ARP_USED_INSTEAD_FLAG, true)
            ->willReturn(null);

        $this->blockReplacementManager->setIsArpUsedInsteadFlag($this->blockMock);
    }

    /**
     * Testing of getIsArpUsedInsteadFlag method
     */
    public function testGetIsArpUsedInsteadFlag()
    {
        $this->blockMock->expects($this->once())
            ->method('getData')
            ->with(BlockReplacementManager::IS_ARP_USED_INSTEAD_FLAG)
            ->willReturn(true);

        $this->assertTrue($this->blockReplacementManager->getIsArpUsedInsteadFlag($this->blockMock));
    }
}
