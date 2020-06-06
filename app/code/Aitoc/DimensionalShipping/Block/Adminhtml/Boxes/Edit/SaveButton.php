<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Block\Adminhtml\Boxes\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Supplier\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save Box'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 100,
        ];
    }
}
