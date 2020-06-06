<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Ui\Component\Form;

use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Aheadworks\Autorelated\Model\Config;

/**
 * Class StatusField
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class StatusField extends Checkbox
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
        $this->addDisabledFlagToConfig();
        parent::prepare();
    }

    /**
     * Adding 'disabled' param to the config of the field
     */
    private function addDisabledFlagToConfig()
    {
        $config = $this->getData('config');
        $config['disabled'] = $this->getIsComponentDisabled();
        $this->setData('config', $config);
    }

    /**
     * Check is need to disable current component
     *
     * @return bool
     */
    private function getIsComponentDisabled()
    {
        return !($this->config->isWvtavFunctionalityEnabled());
    }
}
