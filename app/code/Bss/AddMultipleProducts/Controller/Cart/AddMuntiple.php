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

class AddMuntiple extends \Magento\Checkout\Controller\Cart
{
    protected $productRepository;

    protected $resultPageFactory;

    protected $_layout;

    /**
     * AddMuntiple constructor.
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

    protected function _checkPost($productId, $input)
    {
        $post = $this->getRequest()->getPost();
        if ($post && $post[$productId.'_'.$input]) {
           return $post[$productId.'_'.$input];
        }
        return '';
    }

    protected function _getProductIds()
    {
        $productIds = [];

        if ($this->getRequest()->getPost('product-select')) {
            $productIds = $this->getRequest()->getPost('product-select');
        }
        return $productIds;
    }
    public function execute()
    {

        // if (!$this->_formKeyValidator->validate($this->getRequest())) {
        //     return $this->resultRedirectFactory->create()->setPath('*/*/');
        // }

        $addedProducts = [];
        $product_poup = [];
        $params = $this->getRequest()->getParams();
        $productIds = $this->_getProductIds();
        $storeId = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore()->getId();
        foreach ($productIds as $productId) {
            try {
                $qty = $this->getRequest()->getPost($productId . '_qty', 0);
                $product = $this->productRepository->getById($productId, false, $storeId);
                if ($qty <= 0 || !$product) {
                    continue;
                }
                // nothing to add
                $related = $this->getRequest()->getParam('related_product');
                $_request = $this->getRequest();
                $params['product'] = $productId;
                $params['qty'] = $qty;
                $params['bundle_option'] = $this->_checkPost($productId, 'bundle_option');
                $params['bundle_option_qty'] = $this->_checkPost($productId, 'bundle_option_qty');
                $params['super_attribute'] = $this->_checkPost($productId, 'super_attribute');
                $params['options'] = $this->_checkPost($productId, 'options');
                $params['links'] = $this->_checkPost($productId, 'links');

                $this->cart->addProduct($product, $params);

                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }

                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $addedProducts[] = $product;
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $product_poup['errors'][$product->getId()] = ['qty' => $qty, 'mess' => $e->getMessage()];
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    $product_poup['errors'][$product->getId()] = ['qty' => $qty, 'mess' => end($messages)];
                }
                $cartItem = $this->cart->getQuote()->getItemByProduct($product);
                if ($cartItem) {
                    $this->cart->getQuote()->deleteItem($cartItem);
                }
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }

        $html = $this->_returnCart($addedProducts, $product_poup);

        $result['popup'] = $html;
        $this->getResponse()->setBody(json_encode($result));
        return;
    }

    protected function _returnCart($addedProducts, $product_poup)
    {
        $errormessageCart = '';
        $info_mn_cart = [];
        $before_cart = $this->cart;
        $info_mn_cart['qty'] = $before_cart->getItemsQty();
        $info_mn_cart['subtotal'] = $before_cart->getQuote()->getSubtotal();
        if ($addedProducts) {
            try {
                $this->cart->save()->getQuote()->collectTotals();
                if (!$this->cart->getQuote()->getHasError()) {
                    $products = [];
                    foreach ($addedProducts as $product) {
                        $_item = $this->cart->getQuote()->getItemByProduct($product);
                        $product_poup['success'][] = ['id' => $product->getId(), 'price' => $_item->getPrice()];
                        $products[] = '"' . $product->getName() . '"';
                    }
                    $after_cart = $this->cart;
                    $info_mn_cart['qty'] = $after_cart->getItemsQty();
                    $info_mn_cart['subtotal'] = $after_cart->getQuote()->getSubtotal();
                    $this->messageManager->addSuccess(
                        __('%1 product(s) have been added to shopping cart: %2.', count($addedProducts), join(', ', $products))
                    );
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $this->messageManager->addNotice(
                        \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                    );
                } else {
                    $errormessage = array_unique(explode("\n", $e->getMessage()));
                    $errormessageCart = end($errormessage);
                    $this->messageManager->addError(
                        \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Escaper')->escapeHtml($errormessageCart)
                    );
                }

                $product_poup['success'] = [];
                foreach ($addedProducts as $product) {
                    $product_poup['errors'][$product->getId()] = ['qty' => $this->getRequest()->getPost($product->getId() . '_qty', 0), 'mess' => ''];
                }
            }
        }

        $template = 'Bss_AddMultipleProducts::popup.phtml';
        $html = $this->_layout
            ->createBlock('Bss\AddMultipleProducts\Block\OptionProduct')
            ->setTemplate($template)
            ->setProduct($product_poup)
            ->setCart($info_mn_cart)
            ->setErrorMessageCart($errormessageCart)
            ->setTypeadd('muntiple')
            ->toHtml();
        return $html;
    }
}
