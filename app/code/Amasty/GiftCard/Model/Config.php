<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

class Config
{
    const MODULE = 'amgiftcard';

    const GIFT_CARD_OPTIONS_SECTION = 'card/';

    const ALLOW_THEMSELVES = 'allow_use_themselves';

    const GROUP_DISPLAY_OPTIONS = 'display_options';

    const TIME_ZONE = 'gift_card_timezone';

    const ALLOW_UPLOAD_IMAGES = 'allow_users_upload_own_images';

    const EMAIL_OPTION = 'email';

    const GIFT_CARD_EMAIL_TEMPLATE = 'email_template';

    const GIFT_CARD_TOOLTIP_CONTENT = 'tooltip_content';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getScopeValue($path)
    {
        return $this->scopeConfig->getValue(
            self::MODULE . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getAllowThemselves()
    {
        return $this->getScopeValue(self::GIFT_CARD_OPTIONS_SECTION . self::ALLOW_THEMSELVES);
    }

    /**
     * @return mixed
     */
    public function getSelectedTimeZones()
    {
        return $this->getScopeValue(self::GROUP_DISPLAY_OPTIONS . '/' . self::TIME_ZONE);
    }

    /**
     * @return mixed
     */
    public function getAllowUsersUploadImages()
    {
        return $this->getScopeValue(self::GROUP_DISPLAY_OPTIONS . '/' . self::ALLOW_UPLOAD_IMAGES);
    }

    /**
     * @return mixed
     */
    public function getGiftCardEmailTemplate()
    {
        return $this->getScopeValue(self::EMAIL_OPTION . '/' . self::GIFT_CARD_EMAIL_TEMPLATE);
    }

    /**
     * @return mixed
     */
    public function getTooltipContent()
    {
        return $this->getScopeValue(self::GROUP_DISPLAY_OPTIONS . '/' . self::GIFT_CARD_TOOLTIP_CONTENT);
    }
}
