<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigModel
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeValue($path)
    {
        return $this->scopeConfig->getValue('amgiftcard/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
