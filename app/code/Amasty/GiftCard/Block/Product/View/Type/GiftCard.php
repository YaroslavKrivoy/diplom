<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Block\Product\View\Type;

class GiftCard extends \Magento\Catalog\Block\Product\View\AbstractView
{

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Image\Collection
     */
    protected $imageCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Amasty\GiftCard\Model\Config
     */
    private $config;

    private $enableOptions;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Amasty\GiftCard\Model\ResourceModel\Image\Collection $imageCollection,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Customer\Model\SessionFactory $session,
        \Amasty\GiftCard\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $context->getStoreManager();
        $this->dataHelper = $dataHelper;
        $this->imageCollection = $imageCollection;
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeLists = $localeLists;
        $this->session = $session;
        $this->config = $config;
    }

    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    public function getCurrencyShortName()
    {
        $currency = $this->getCurrency();
        return $currency->getShortName() ? $currency->getShortName() : $currency->getSymbol();
    }

    public function getCurrencySymbol()
    {
        $currency = $this->getCurrency();
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    protected function getCurrency()
    {
        $store = $this->getStore();
        return $this->localeCurrency->getCurrency($store->getCurrentCurrencyCode());
    }

    public function isPredefinedAmount()
    {
        return count($this->getListAmounts()) >= 0;
    }

    public function getListAmounts()
    {
        $product = $this->getProduct();
        $listAmounts = [];
        foreach ($product->getPriceModel()->getAmounts($product) as $amount) {
            $listAmounts[] = (float)$amount['website_value'];
        }
        return $listAmounts;
    }

    public function getFormatPrice($price)
    {
        return $this->dataHelper->convertAndFormatPrice($price);
    }

    public function convertPrice($price)
    {
        return $this->dataHelper->convertPrice($price);
    }

    public function isConfigured()
    {
        $product = $this->getProduct();
        if (!$product->getAmAllowOpenAmount() && !$this->getListAmounts()) {
            return false;
        }
        return true;
    }

    public function isMultiAmount()
    {
        $product = $this->getProduct();
        return $product->getPriceModel()->isMultiAmount($product);
    }

    public function getImages()
    {
        $product = $this->getProduct();
        $imageIds = $product->getAmGiftcardCodeImage();
        $this->imageCollection
            ->addFieldToFilter('image_id', ['in' => $imageIds])
            ->addFieldToFilter('active', \Amasty\GiftCard\Model\Image::STATUS_ACTIVE);

        return $this->imageCollection;
    }

    public function getListCardTypes()
    {
        return $this->dataHelper->getCardTypes();
    }

    public function getScopeConfigByPath($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getListTimezones()
    {
        $result = [];
        $allTimeZones = $this->localeLists->getOptionTimezones();
        $selectedTimeZones = $this->config->getSelectedTimeZones();

        if (!$selectedTimeZones) {
            return $allTimeZones;
        }

        $selectedTimeZones = explode(',', $selectedTimeZones);

        foreach ($allTimeZones as $timeZone) {
            if (in_array($timeZone['value'], $selectedTimeZones)) {
                $result[] = [
                    'value' => $timeZone['value'],
                    'label' => $timeZone['label']
                ];
            }
        }

        return $result;
    }

    public function isMessageAvailable($product)
    {
        $amAllowMessage = $this->dataHelper->getValueOrConfig(
            $product->getAmAllowMessage(),
            'amgiftcard/card/allow_message'
        );
        return $amAllowMessage;
    }

    public function getDefaultValue($key)
    {
        $options = $this->getProduct()->getPreconfiguredValues()->getOptions();

        return isset($options[$key]) ? (string)$options[$key] : null;
    }

    protected function getCustomerSession()
    {
        return $this->session->create();
    }

    /**
     * @param $amount
     *
     * @return float
     */
    public function formatPrice($amount)
    {
        return $this->dataHelper->formatPrice($amount);
    }

    /**
     * @param $amount
     *
     * @return float
     */
    public function round($amount)
    {
        return $this->dataHelper->round($amount);
    }

    /**
     * @return mixed
     */
    public function getAllowedOptions()
    {
        return $this->config->getScopeValue('display_options/show_options');
    }

    /**
     * @param $option
     * @return string
     */
    public function isOptionEnable($option)
    {
        if (!$this->enableOptions) {
            $this->enableOptions = $this->getAllowedOptions();
        }

        return strstr($this->enableOptions, $option);
    }

    /**
     * @return mixed
     */
    public function getAllowUsersUploadImages()
    {
        return $this->config->getAllowUsersUploadImages();
    }

    /**
     * @return mixed
     */
    public function getTooltipContent()
    {
        return $this->config->getTooltipContent();
    }
}
