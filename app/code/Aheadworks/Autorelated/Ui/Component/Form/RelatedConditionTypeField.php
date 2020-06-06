<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Ui\Component\Form;

use Magento\Ui\Component\Form\Element\Select;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Autorelated\Model\Config;
use Aheadworks\Autorelated\Model\Source\ProductConditionType;

/**
 * Class RelatedConditionTypeField
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class RelatedConditionTypeField extends Select
{
    /**#@+
     * Service constants for additional params in the field config
     */
    const WVTAV_FUNCTIONALITY_ENABLED_FLAG_CONFIG_KEY = 'isWvtavFunctionalityEnabled';
    const WVTAV_CONDITION_TYPE_VALUE_CONFIG_KEY = 'wvtavConditionTypeValue';
    const WVTAV_FUNCTIONALITY_ALERT_CONTENT_CONFIG_KEY = 'wvtavFunctionalityAlertContent';
    const MODULE_CONFIG_SECTION_ID = 'aw_arp';
    /**#@-*/

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param array|OptionSourceInterface|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $this->addCustomParamsToConfig();
        parent::prepare();
    }

    /**
     * Adding custom params to the config of the field
     */
    private function addCustomParamsToConfig()
    {
        $config = $this->getData('config');
        $config[self::WVTAV_FUNCTIONALITY_ENABLED_FLAG_CONFIG_KEY] = $this->config->isWvtavFunctionalityEnabled();
        $config[self::WVTAV_CONDITION_TYPE_VALUE_CONFIG_KEY] = ProductConditionType::WHO_VIEWED_THIS_ALSO_VIEWED;
        $config[self::WVTAV_FUNCTIONALITY_ALERT_CONTENT_CONFIG_KEY] = __(
            'You need to enable this functionality in the extension <a href="%1">settings</a>',
            $this->config->getModuleSettingsPageUrl()
        );
        $this->setData('config', $config);
    }
}
