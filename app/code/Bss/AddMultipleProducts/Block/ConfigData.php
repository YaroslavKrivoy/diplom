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
namespace Bss\AddMultipleProducts\Block;

use Magento\Framework\View\Element\Template\Context;
use Bss\AddMultipleProducts\Helper\Data as HelperData;

class ConfigData extends \Magento\Framework\View\Element\Template
{
    private $helperData;

    private $customerSession;

    private $taxConfig;

    /**
     * ConfigData constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\AddMultipleProducts\Helper\Data $helperData
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->taxConfig = $taxConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getGroupCustomerId()
    {
        $customer = $this->customerSession->create();
        $group_Id = 0;
        if ($customer->isLoggedIn()) {
            $group_Id = $customer->getCustomer()->getGroupId();
        }
        return $group_Id;
    }

    /**
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->taxConfig->priceIncludesTax($this->_storeManager->getStore());
    }

    /**
     * @return string
     */
    public function getUrlMediaStick()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     */
    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * @return string
     */
    public function geturlAddToCart()
    {
        return $this->getUrl('addmuntiple/cart/add');
    }

    /**
     * @return string
     */
    public function geturlAddMultipleToCart()
    {
        return $this->getUrl('addmuntiple/cart/addMuntiple');
    }

    /**
     * @return mixed
     */
    public function backGroundStick()
    {
        return $this->helperData->backGroundStick();
    }

    /**
     * @return mixed
     */
    public function displayAddmunltiple()
    {
        return $this->helperData->displayAddmunltiple();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @return array|bool
     */
    public function getCustomerGroup()
    {
        return $this->helperData->getCustomerGroup();
    }

    /**
     * @return bool
     */
    public function showSelectProduct()
    {
        return $this->helperData->showSelectProduct();
    }

    /**
     * @return bool
     */
    public function showStick()
    {
        return $this->helperData->showStick();
    }

    /**
     * @return mixed
     */
    public function positionButton()
    {
        return $this->helperData->positionButton();
    }

    /**
     * @return mixed|string
     */
    public function getTextbuttonaddmt()
    {
        return $this->helperData->getTextbuttonaddmt();
    }

    /**
     * @return int
     */
    public function defaultQty()
    {
        return $this->helperData->defaultQty();
    }

    /**
     * @return bool
     */
    public function showTotal()
    {
        return $this->helperData->showTotal();
    }
}
