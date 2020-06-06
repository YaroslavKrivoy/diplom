<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Form;

use Aheadworks\Autorelated\Model\Config;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class StoreField
 *
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class CustomerSegmentField extends MultiSelect
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param array|null $options
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
        $config = $this->getData('config');
        if (!$this->config->isEnterpriseCustomerSegmentInstalled()
            || !$this->config->isEnterpriseCustomerSegmentEnabled()
        ) {
            $config['visible'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
