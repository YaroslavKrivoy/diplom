<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Aheadworks\Autorelated\Controller\Adminhtml\Rule\Save;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Autorelated\Model\Converter\Condition as ConditionConverter;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Template;
use Aheadworks\Autorelated\Model\Source\Sort;
use Aheadworks\Autorelated\Api\Data\ConditionInterface;

/**
 * Test for \Aheadworks\Autorelated\Controller\Adminhtml\Rule\Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Save
     */
    private $save;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var RuleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleDataFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var DataPersistorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataPersistorMock;

    /**
     * @var ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * @var TypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleTypeResolverMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var array
     */
    private $formData = [
        'id' => 1,
        'position' => Position::PRODUCT_CONTENT_TOP,
        'rule' => [
            'viewed' => [
                'viewed_conditions' => [
                    'type' => \Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ]
            ],
            'related' => [
                'related_conditions' => [
                    'type' => \Aheadworks\Autorelated\Model\Rule\Related\Condition\Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ]
            ],
            'category_related' => [
                'category_related_conditions' => [
                    'type' => \Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ]
            ],
        ]
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Http::class)
            ->setMethods(['getPostValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->ruleDataFactoryMock = $this->getMockBuilder(RuleInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataPersistorMock = $this->getMockForAbstractClass(DataPersistorInterface::class);
        $this->conditionConverterMock = $this->getMockBuilder(ConditionConverter::class)
            ->setMethods(['arrayToDataModel'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleTypeResolverMock = $this->getMockBuilder(TypeResolver::class)
            ->setMethods(['getType'])
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->save = $objectManager->getObject(
            Save::class,
            [
                'context' => $contextMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'ruleDataFactory' => $this->ruleDataFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'storeManager' => $this->storeManagerMock,
                'dataPersistor' => $this->dataPersistorMock,
                'conditionConverter' => $this->conditionConverterMock,
                'ruleTypeResolver' => $this->ruleTypeResolverMock
            ]
        );
    }

    /**
     * Testing of execute method, redirect if get data from form is empty
     */
    public function testExecuteRedirect()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(null);

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->save->execute());
    }

    /**
     * Testing of execute method, redirect if error is occur
     */
    public function testExecuteRedirectError()
    {
        $exception = new \Exception;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);
        $this->ruleTypeResolverMock->expects($this->exactly(2))
            ->method('getType')
            ->with($this->formData['position'])
            ->willReturn(Type::PRODUCT_BLOCK_TYPE);
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->exactly(2))
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->formData['id'])
            ->willReturn($ruleMock);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ruleMock)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception);
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->save->execute());
    }
}
