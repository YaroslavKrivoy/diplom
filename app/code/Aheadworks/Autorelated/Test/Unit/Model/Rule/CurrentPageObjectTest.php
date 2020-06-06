<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Model\Rule;

use Aheadworks\Autorelated\Model\Source\Type as SourceType;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Autorelated\Model\Rule\CurrentPageObject;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Rule;
use Aheadworks\Autorelated\Model\Rule\Related\Product as RelatedProduct;
use Aheadworks\Autorelated\Model\Rule\Viewed\Product as ViewedProduct;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as QuoteItemCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Validator as ConditionValidator;

/**
 * Test for \Aheadworks\Autorelated\Model\Rule\CurrentPageObject
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CurrentPageObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CurrentPageObject
     */
    private $currentPageObject;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var CheckoutSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registryMock;

    /**
     * @var ConditionValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionValidatorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->checkoutSessionMock = $this->getMockBuilder(CheckoutSession::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->createPartialMock(Registry::class, ['registry']);
        $this->conditionValidatorMock = $this->createPartialMock(ConditionValidator::class, ['isProductValid']);
        $this->currentPageObject = $objectManager->getObject(
            CurrentPageObject::class,
            [
                'request' => $this->requestMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'registry' => $this->registryMock,
                'conditionValidator' => $this->conditionValidatorMock
            ]
        );
    }

    /**
     * Testing of getCurrentProductIdForBlock method for product block type
     */
    public function testGetCurrentProductIdForProductBlockType()
    {
        $productId = 1;
        $ruleId = 5;
        $type = SourceType::PRODUCT_BLOCK_TYPE;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($ruleId);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->willReturn(null);
        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->withConsecutive(
                ['product_id'],
                ['id'],
                ['id']
            )
            ->willReturnOnConsecutiveCalls(null, $productId, $productId);

        $this->assertEquals($productId, $this->currentPageObject->getCurrentProductIdForBlock($ruleMock, $type));
    }

    /**
     * Testing of getCurrentProductIdForBlock method for cart block type
     */
    public function testGetCurrentProductIdForCartBlockType()
    {
        $productId = 5;
        $quoteId = 1;
        $ruleId = 6;
        $type = SourceType::CART_BLOCK_TYPE;

        $viewedMatchingProductIds = [4, 2, $productId];
        $relatedMatchingProductIds = [1, 2, 3];
        $quoteProductIds = [
            ['product_id' => 1],
            ['product_id' => $productId]
        ];

        $selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(['order'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemCollectionMock = $this->getMockBuilder(QuoteItemCollection::class)
            ->setMethods(['getSelect', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteItemCollectionMock->expects($this->once())
            ->method('getSelect')
            ->willReturn($selectMock);
        $quoteItemCollectionMock->expects($this->once())
            ->method('getData')
            ->willReturn($quoteProductIds);

        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getId', 'getItemsCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $quoteMock->expects($this->exactly(2))
            ->method('getItemsCollection')
            ->willReturn($quoteItemCollectionMock);

        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->conditionValidatorMock->expects($this->exactly(2))
            ->method('isProductValid')
            ->withConsecutive([$ruleMock, 1], [$ruleMock, $productId])
            ->willReturnOnConsecutiveCalls(false, true);

        $this->assertEquals($productId, $this->currentPageObject->getCurrentProductIdForBlock($ruleMock, $type));
    }
}
