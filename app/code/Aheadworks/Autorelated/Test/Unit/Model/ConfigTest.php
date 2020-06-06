<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model;

use Aheadworks\Autorelated\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Test for \Aheadworks\Autorelated\Model\Config
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $configModel;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var ModuleManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->moduleManagerMock = $this->createPartialMock(ModuleManager::class, ['isEnabled']);
        $this->configModel = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'moduleManager' => $this->moduleManagerMock
            ]
        );
    }

    /**
     * Test isShowingMultipleBlocksAllowed method
     */
    public function testIsShowingMultipleBlocksAllowed()
    {
        $storeId = 1;
        $flag = true;
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_SHOW_MULTIPLE_BLOCKS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($flag);
        $this->assertEquals($flag, $this->configModel->isShowingMultipleBlocksAllowed($storeId));
    }

    /**
     * Test getNumberDaysBuildBestsellersList method
     */
    public function testGetNumberDaysBuildBestsellersList()
    {
        $expected = 30;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_NUMBER_DAYS_BUILD_BESTSELLERS_LIST)
            ->willReturn($expected);
        $this->assertEquals($expected, $this->configModel->getNumberDaysBuildBestsellersList());
    }

    /**
     * Test isEnterpriseCustomerSegmentInstalled method
     */
    public function testIsEnterpriseCustomerSegmentInstalled()
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with("Magento_CustomerSegment")
            ->willReturn(true);
        $this->assertTrue($this->configModel->isEnterpriseCustomerSegmentInstalled());
    }

    /**
     * Test isEnterpriseCustomerSegmentEnabled method
     */
    public function testIsEnterpriseCustomerSegmentEnabled()
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with("Magento_CustomerSegment")
            ->willReturn(false);
        $this->assertFalse($this->configModel->isEnterpriseCustomerSegmentEnabled());
    }
}
