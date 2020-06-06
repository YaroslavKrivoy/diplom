<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\UrlInterface;

/**
 * Class Config
 * @package Aheadworks\Autorelated\Model
 */
class Config
{
    /**
     * Module config section id for generating module settings page url
     */
    const MODULE_CONFIG_SECTION_ID = 'aw_arp';

    /**
     * Configuration path to is show more than one block in one position flag
     */
    const XML_PATH_SHOW_MULTIPLE_BLOCKS = 'aw_arp/general/show_miltiple_blocks';

    /**
     * Configuration path to a number of days to build a list of bestsellers
     */
    const XML_PATH_NUMBER_DAYS_BUILD_BESTSELLERS_LIST = 'aw_arp/general/number_days_build_bestsellers_list';

    /**
     * Configuration path to is WVTAV functionality enabled
     */
    const XML_PATH_WVTAV_ENABLE_FUNCTIONALITY = 'aw_arp/wvtav/enable_functionality';

    /**
     * Configuration path to WVTAV process sessions period parameter
     */
    const XML_PATH_WVTAV_PROCESS_SESSIONS_PERIOD = 'aw_arp/wvtav/process_sessions_period';

    /**
     * Enterprise customer segment table name
     */
    const CUSTOMER_SEGMENT_TABLE_NAME = 'magento_customersegment_segment';

    /**
     * Enterprise customer segment module name
     */
    const CUSTOMER_SEGMENT_MODULE_NAME  = 'Magento_CustomerSegment';

    /**
     * Enterprise customer segment helper class name
     */
    const CUSTOMER_SEGMENT_HELPER_CLASS_NAME  = '\Magento\CustomerSegment\Helper\Data';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Module manager
     *
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleManager $moduleManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleManager $moduleManager,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Check if showing more than one block in one position allowed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowingMultipleBlocksAllowed($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_MULTIPLE_BLOCKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve number of days to build a list of bestsellers
     *
     * @return int
     */
    public function getNumberDaysBuildBestsellersList()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_NUMBER_DAYS_BUILD_BESTSELLERS_LIST
        );
    }

    /**
     * Check if EE customer segment module installed
     *
     * @return bool
     */
    public function isEnterpriseCustomerSegmentInstalled()
    {
        return $this->moduleManager->isEnabled(self::CUSTOMER_SEGMENT_MODULE_NAME);
    }

    /**
     * Check whether enterprise customer segment functionality should be enabled
     *
     * @return bool
     */
    public function isEnterpriseCustomerSegmentEnabled()
    {
        $flag = false;
        if ($this->isEnterpriseCustomerSegmentInstalled()) {
            $enterpriseSegmentsHelper = $this->getEnterpriseCustomerSegmentHelper();
            if (is_object($enterpriseSegmentsHelper)) {
                $flag = $enterpriseSegmentsHelper->isEnabled();
            }
        }
        return $flag;
    }

    /**
     * Retrieve instance of customer segment helper class
     *
     * @return mixed|null
     */
    public function getEnterpriseCustomerSegmentHelper()
    {
        $enterpriseSegmentsHelper = null;
        if ($this->isEnterpriseCustomerSegmentInstalled()) {
            $enterpriseSegmentsHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(self::CUSTOMER_SEGMENT_HELPER_CLASS_NAME)
            ;
        }
        return $enterpriseSegmentsHelper;
    }

    /**
     * Check if WVTAV functionality enabled
     *
     * @return bool
     */
    public function isWvtavFunctionalityEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_WVTAV_ENABLE_FUNCTIONALITY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get WVTAV process sessions period parameter value (in days)
     *
     * @return int|null
     */
    public function getWvtavProcessSessionsPeriod()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_WVTAV_PROCESS_SESSIONS_PERIOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve url of module settings page
     *
     * @return string
     */
    public function getModuleSettingsPageUrl()
    {
        return $this->urlBuilder->getUrl($this->getModuleSettingsRoutePath());
    }

    /**
     * Retrieve route path of module settings page
     *
     * @return string
     */
    private function getModuleSettingsRoutePath()
    {
        return 'adminhtml/system_config/edit/section/' . self::MODULE_CONFIG_SECTION_ID;
    }
}
