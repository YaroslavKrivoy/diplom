<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Listing\Columns;

use Aheadworks\Autorelated\Model\Source\Type;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class ProductConditionType
 * @package Aheadworks\Autorelated\Ui\Component\Listing\Columns
 */
class ProductConditionType extends Column
{
    /**
     * @var Type
     */
    private $typeSource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Type $typeSource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Type $typeSource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->typeSource = $typeSource;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');
        $config['tooltip']['description'] = $this->typeSource->getProductConditionTypeTooltip();
        $this->setData('config', $config);
    }
}
