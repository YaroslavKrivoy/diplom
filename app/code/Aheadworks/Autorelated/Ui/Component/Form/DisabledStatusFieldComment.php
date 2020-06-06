<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Ui\Component\Form;

use Magento\Ui\Component\Container;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Aheadworks\Autorelated\Model\Config;

/**
 * Class DisabledStatusFieldComment
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class DisabledStatusFieldComment extends Container
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $this->addLabelToConfig();
        parent::prepare();
    }

    /**
     * Adding label to the config of the field
     */
    private function addLabelToConfig()
    {
        $config = $this->getData('config');
        $config['label'] = __(
            'Please enable the "Who Viewed This Also Viewed" functionality in the <a href="%1">settings</a> or choose' .
            ' another mode in the "What to display" tab to enable the rule.',
            $this->config->getModuleSettingsPageUrl()
        );
        $this->setData('config', $config);
    }
}
