<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin;

class DefaultConfigProvider
{

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    private $dataHelper;

    public function __construct(\Amasty\GiftCard\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param $config
     *
     * @return mixed
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $config)
    {
        $config['giftCard']['is_enable'] = $this->dataHelper->isEnableGiftFormInCart();

        return $config;
    }
}
