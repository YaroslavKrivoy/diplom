<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Form;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class StoreField
 *
 * @package Aheadworks\Autorelated\Ui\Component\Form
 */
class StoreField extends MultiSelect
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($this->storeManager->isSingleStoreMode()) {
            $config['visible'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
