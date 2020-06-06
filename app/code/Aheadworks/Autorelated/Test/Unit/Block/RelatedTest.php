<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Test\Unit\Block;

use Aheadworks\Autorelated\Block\Related;
use Aheadworks\Autorelated\Model\Source\Position;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Autorelated\Api\BlockRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\Url\EncoderInterface;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Template;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\FormKey as FormKeyView;

/**
 * Test for \Aheadworks\Autorelated\Block\Related
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var PostHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postHelperMock;

    /**
     * @var ImageBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageBuilderMock;

    /**
     * @var CartHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHelperMock;

    /**
     * @var EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlEncoderMock;

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

        $this->blocksRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepositoryInterface::class);
        $this->postHelperMock = $this->getMockBuilder(PostHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->imageBuilderMock = $this->getMockBuilder(ImageBuilder::class)
            ->setMethods(['setProduct', 'setImageId', 'setAttributes', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->cartHelperMock = $this->getMockBuilder(CartHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlEncoderMock = $this->getMockBuilder(EncoderInterface::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['isAjax']
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock
            ]
        );

        $this->related = $objectManager->getObject(
            Related::class,
            [
                'context' => $contextMock,
                'blocksRepository' => $this->blocksRepositoryMock,
                'productRepository' => $this->productRepositoryMock,
                'postHelper' => $this->postHelperMock,
                'imageBuilder' => $this->imageBuilderMock,
                'cartHelper' => $this->cartHelperMock,
                'urlEncoder' => $this->urlEncoderMock
            ]
        );
    }

    /**
     * Testing of isAjax method
     *
     * @param bool $isAjax
     * @param bool $expected
     * @dataProvider isAjaxDataProvider
     */
    public function testIsAjax($isAjax, $expected)
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn($isAjax);

        $this->assertEquals($expected, $this->related->isAjax());
    }

    /**
     * Data provider for testIsAjax method
     *
     * @return array
     */
    public function isAjaxDataProvider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }

    /**
     * Testing of getBlocks method
     */
    public function testGetBlocks()
    {
        $this->related->setNameInLayout('awarp_related_before_product');
        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
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
     * Testing of getPostDataHelper method
     */
    public function testGetPostDataHelper()
    {
        $this->assertSame($this->postHelperMock, $this->related->getPostDataHelper());
    }

    /**
     * Testing of isGridTemplate method
     *
     * @param int $templateId
     * @param bool $expected
     * @dataProvider isGridTemplateDataProvider
     */
    public function testIsGridTemplate($templateId, $expected)
    {
        $this->assertEquals($expected, $this->related->isGridTemplate($templateId));
    }

    /**
     * @return array
     */
    public function isGridTemplateDataProvider()
    {
        return [
            [Template::SLIDER, false],
            [Template::GRID, true]
        ];
    }

    /**
     * Testing of getProductById method
     */
    public function testGetProductById()
    {
        $productId = 1;
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);
        $this->assertSame($productMock, $this->related->getProductById($productId));
    }

    /**
     * Testing of getProductById method if exception occur
     */
    public function testGetProductByIdException()
    {
        $productId = 1;
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException);
        $this->assertNull($this->related->getProductById($productId));
    }

    /**
     * Testing of getImage method on return true
     */
    public function testGetImage()
    {
        $imageId = 'product_base_image';
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $productImageMock = $this->getMockBuilder(Image::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->imageBuilderMock->expects($this->once())
            ->method('setProduct')
            ->with($productMock)
            ->willReturnSelf();
        $this->imageBuilderMock->expects($this->once())
            ->method('setImageId')
            ->with($imageId)
            ->willReturnSelf();
        $this->imageBuilderMock->expects($this->once())
            ->method('setAttributes')
            ->willReturnSelf();
        $this->imageBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($productImageMock);

        $this->assertSame($productImageMock, $this->related->getImage($productMock, $imageId));
    }

    /**
     * Testing of getProductPrice method on return true
     */
    public function testGetProductPrice()
    {
        $priceRenderHtml = 'product price html code';
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock = $this->getMockBuilder(PricingRender::class)
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();
        $blockMock->expects($this->once())
            ->method('render')
            ->willReturn($priceRenderHtml);
        $layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn(null);
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                PricingRender::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices', 'use_link_for_as_low_as' => true]]
            )->willReturn($blockMock);

        $this->related->setNameInLayout('awarp_related_before_product');

        $this->related->setLayout($layoutMock);

        $this->assertEquals($priceRenderHtml, $this->related->getProductPrice($productMock));
    }

    /**
     * Testing of encodeUrl method
     */
    public function testEncodeUrl()
    {
        $url = 'https://ecommerce.aheadworks.com/';
        $urlExpected = base64_encode($url);

        $this->urlEncoderMock->expects($this->once())
            ->method('encode')
            ->with($url)
            ->willReturn($urlExpected);

        $this->assertEquals($urlExpected, $this->related->encodeUrl($url));
    }

    /**
     * Testing of hasProductUrl method
     *
     * @param bool $value
     * @param bool $expected
     * @dataProvider hasProductUrlDataProvider
     */
    public function testHasProductUrlFalse($value, $expected)
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getVisibleInSiteVisibilities'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getVisibleInSiteVisibilities')
            ->willReturn($value);

        $class = new \ReflectionClass($this->related);
        $method = $class->getMethod('hasProductUrl');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->related, $productMock));
    }

    /**
     * @return array
     */
    public function hasProductUrlDataProvider()
    {
        return [
            [false, false],
            [true, true]
        ];
    }

    /**
     * Testing of getBlockPosition method
     */
    public function testGetBlockPosition()
    {
        $this->related->setNameInLayout('awarp_related_before_product');
        $class = new \ReflectionClass($this->related);
        $method = $class->getMethod('getBlockPosition');
        $method->setAccessible(true);

        $this->assertEquals(Position::PRODUCT_BEFORE_NATIVE_RELATED_BLOCK, $method->invoke($this->related));
    }

    /**
     * Testing of getBlockType method
     */
    public function testGetBlockType()
    {
        $this->related->setNameInLayout('awarp_related_before_product');
        $class = new \ReflectionClass($this->related);
        $method = $class->getMethod('getBlockType');
        $method->setAccessible(true);

        $this->assertEquals(Type::PRODUCT_BLOCK_TYPE, $method->invoke($this->related));
    }

    /**
     * Testing of getFormKeyHtml method on return true
     */
    public function testGetFormKeyHtml()
    {
        $formKeyHtml = 'form key html code';

        $blockMock = $this->getMockBuilder(FormKeyView::class)
            ->setMethods(['toHtml'])
            ->disableOriginalConstructor()
            ->getMock();
        $blockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($formKeyHtml);
        $layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('formkey')
            ->willReturn(null);
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                FormKeyView::class,
                'formkey'
            )->willReturn($blockMock);

        $this->related->setNameInLayout('awarp_related_before_product');

        $this->related->setLayout($layoutMock);

        $this->assertEquals($formKeyHtml, $this->related->getFormKeyHtml());
    }
}
