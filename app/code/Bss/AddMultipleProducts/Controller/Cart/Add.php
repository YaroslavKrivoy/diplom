<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AddMultipleProducts
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AddMultipleProducts\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart
{

    protected $productRepository;
    protected $resultPageFactory;
    protected $_layout;

    /**
     * Add constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository
    ) {
        $this->_layout = $layout;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
    }

    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    public function execute()
    {

        // if (!$this->_formKeyValidator->validate($this->getRequest())) {
        //     return $this->resultRedirectFactory->create()->setPath('*/*/');
        // }
        $info_mn_cart = [];
        $before_cart = $this->cart;
        $info_mn_cart['qty'] = $before_cart->getItemsQty();
        $info_mn_cart['subtotal'] = $before_cart->getQuote()->getSubtotal();
        $params = $this->getRequest()->getParams();
        $result = [];
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => \Magento\Framework\App\ObjectManager::getInstance()->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            if (!$product) {
                return $this->goBack();
            }

            $this->cart->addProduct($product, $params);

            $relatedAdded = false;
            if (!empty($related)) {
                $relatedAdded = true;
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $after_cart = $this->cart;
                    $info_mn_cart['qty'] = $after_cart->getItemsQty();
                    $info_mn_cart['subtotal'] = $after_cart->getQuote()->getSubtotal();
                    $cartItem = $this->cart->getQuote()->getItemByProduct($product);
                    $template = 'Bss_AddMultipleProducts::popup.phtml';
                    $html = $this->_layout
                                ->createBlock('Bss\AddMultipleProducts\Block\OptionProduct')
                                ->setTemplate($template)
                                ->setProduct($product)
                                ->setCart($info_mn_cart)
                                ->setPrice($cartItem->getPrice())
                                ->setTypeadd('single')
                                ->toHtml();
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $result['popup'] = $html;
                    $this->messageManager->addSuccessMessage($message);
                    $this->getResponse()->setBody(json_encode($result));
                    return;
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
                $product_fail[$product->getId()] = ['qty'=> $params['qty'],'mess'=>$e->getMessage()];
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
                $product_fail[$product->getId()] = ['qty'=> $params['qty'],'mess'=>end($messages)];
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            $product_poup['errors'] = $product_fail;
            $template = 'Bss_AddMultipleProducts::popup.phtml';
            $html = $this->_layout
                        ->createBlock('Bss\AddMultipleProducts\Block\OptionProduct')
                        ->setTemplate($template)
                        ->setProduct($product_poup)
                        ->setCart($info_mn_cart)
                        ->setTypeadd('single')
                        ->toHtml();
            $message = __(
                'You added %1 to your shopping cart.',
                $product->getName()
            );
            $result['popup'] = $html;
            $this->getResponse()->setBody(json_encode($result));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical($e);
            $this->getResponse()->setBody(json_encode($result));
            return;
        }
    }
}
