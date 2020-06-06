<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Layout;

use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\Reader\Context;

class LayoutMoveDirectiveChange
{
    const ULTIMO_THEME_CODE = 'Infortis/ultimo';

    const CHECKOUT_CART_INDEX_ACTION_NAME = 'checkout_cart_index';

    const AMGIFTCARD_LAYOUT_BLOCK_NAME = 'checkout.cart.amgiftcard';

    const CART_COUPON_BLOCK_NAME = 'checkout.cart.coupon';

    const CART_ITEMS_BLOCK_NAME = 'checkout.cart.items';

    const CART_TOTALS_CONTAINER_BLOCK_NAME = 'checkout.cart.totals.container';

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\View\Design\ThemeInterface
     */
    private $themeInfo;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * LayoutMoveDirectiveChange constructor.
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\View\Design\ThemeInterface $themeInfo
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\Design\ThemeInterface $themeInfo,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->appState = $appState;
        $this->themeInfo = $themeInfo;
        $this->themeProvider = $themeProvider;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @param $context
     * @param Context $readerContext
     * @param Element $currentElement
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeInterpret($context, Context $readerContext, Element $currentElement)
    {
        $imMoveableBlocks = [self::CART_COUPON_BLOCK_NAME, self::AMGIFTCARD_LAYOUT_BLOCK_NAME];
        $themeCode = (string)$this->getCurrentThemeCode();
        if ($this->isFrontend()
            && $this->request->getFullActionName() === self::CHECKOUT_CART_INDEX_ACTION_NAME
            && $themeCode
            && $themeCode === self::ULTIMO_THEME_CODE
            && in_array($currentElement->getAttribute('element'), $imMoveableBlocks)
        ) {
            switch ($currentElement->getAttribute('element')) {
                case self::AMGIFTCARD_LAYOUT_BLOCK_NAME:
                    $currentElement->setAttribute('destination', self::CART_ITEMS_BLOCK_NAME);
                    break;
                case self::CART_COUPON_BLOCK_NAME:
                    $currentElement->setAttribute('destination', self::CART_TOTALS_CONTAINER_BLOCK_NAME);
                    break;
            }
        }

        return [$readerContext, $currentElement];
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isFrontend()
    {
        return $this->appState->getAreaCode() == 'frontend';
    }

    /**
     * @return string
     */
    private function getCurrentThemeCode()
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->themeProvider->getThemeById($themeId);

        return $theme->getId() ? $theme->getCode() : '';
    }
}
