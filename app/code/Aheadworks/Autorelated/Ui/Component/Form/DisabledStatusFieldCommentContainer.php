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
 * Class DisabledStatusFieldCommentContainer
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class DisabledStatusFieldCommentContainer extends Container
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
        $this->addVisibleFlagToConfig();
        parent::prepare();
    }

    /**
     * Adding 'visible' param to the config of the field
     */
    private function addVisibleFlagToConfig()
    {
        $config = $this->getData('config');
        $config['visible'] = $this->getIsComponentVisible();
        $this->setData('config', $config);
    }

    /**
     * Check is need to show current component
     *
     * @return bool
     */
    private function getIsComponentVisible()
    {
        return !($this->config->isWvtavFunctionalityEnabled());
    }
}
