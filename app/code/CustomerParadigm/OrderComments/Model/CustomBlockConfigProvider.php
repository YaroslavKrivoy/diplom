<?php
/**
 * OrderComments : CustomBlockConfigProvider.php
 * @copyright Customer Paradigm. 2018
 * @date 2/5/18 7:57 AM
 * @version 1.0.2
 * @since 1.0.0
 */

namespace CustomerParadigm\OrderComments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Cart;

/**
 * Class CustomBlockConfigProvider
 * @package CustomerParadigm\OrderComments\Model
 */
class CustomBlockConfigProvider implements ConfigProviderInterface
{
    /** @var
     * \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfiguration;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
     * @param Cart $cart
     * @internal param QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
        Cart $cart
    ) {
        $this->scopeConfiguration = $scopeConfiguration;
        $this->cart = $cart;
    }

    /**
     * @return array() $showHide
     */
    public function getConfig()
    {
        /** @var array() $showHide */
        $config = [];
        /** @var boolean $enabled */
        $enabled = $this->scopeConfiguration
            ->getValue(
                'checkout/options/onepage_checkout_comments_enabled',
                ScopeInterface::SCOPE_STORE
            );
        $config['show_hide_custom_block'] = ($enabled)?true:false;
        $_quote = $this->cart->getQuote();
        $config['quote_id'] = $_quote->getId();

        return $config;
    }
}
