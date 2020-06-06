<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\CustomerStatistic;

use Aheadworks\Autorelated\Model\CustomerStatistic\Manager;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Autorelated\Model\CustomerStatistic\Manager
 */
class ManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * SessionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $sessionData = [
            'arp_view_1' => time()+50000,
            'arp_view_2' => time()+50000,
            'arp_view_3' => time(),
            'arp_click_1' => time()+50000,
            'arp_click_3' => time(),
        ];

        $this->sessionManagerMock = $this->getMockBuilder(SessionManager::class)
            ->setMethods(['getData', 'setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionManagerMock->expects($this->any())
            ->method('getData')
            ->willReturn($sessionData);

        $this->manager = $objectManager->getObject(
            Manager::class,
            ['sessionManager' => $this->sessionManagerMock]
        );
    }

    /**
     * Testing of getActions method
     */
    public function testGetActions()
    {
        $count = 3;
        $class = new \ReflectionClass($this->manager);
        $method = $class->getMethod('getActions');
        $method->setAccessible(true);

        $this->assertCount($count, $method->invoke($this->manager));
    }

    /**
     * Testing of isSetAction method
     *
     * @param string $actionName
     * @param bool $expected
     * @dataProvider isSetActionDataProvider
     */
    public function testIsSetAction($actionName, $expected)
    {
        $this->assertEquals($expected, $this->manager->isSetAction($actionName));
    }

    /**
     * @return array
     */
    public function isSetActionDataProvider()
    {
        return [
            ['arp_view_1', true],
            ['arp_view_2', true],
            ['arp_click_1', true],
            ['arp_view_3', false],
            ['arp_click_2', false],
            ['arp_click_3', false],
        ];
    }

    /**
     * Testing of addAction method
     */
    public function testAddAction()
    {
        $count = 4;
        $actionName = 'arp_view_5';
        $class = new \ReflectionClass($this->manager);
        $method = $class->getMethod('getActions');
        $method->setAccessible(true);
        $method->invoke($this->manager);

        $this->manager->addAction($actionName);
        $this->assertCount($count, $method->invoke($this->manager));
    }
}
