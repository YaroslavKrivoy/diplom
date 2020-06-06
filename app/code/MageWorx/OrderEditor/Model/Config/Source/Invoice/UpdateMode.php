<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model\Config\Source\Invoice;

use \Magento\Framework\Option\ArrayInterface;

class UpdateMode implements ArrayInterface
{
    const MODE_UPDATE_ADD = 'add';
    const MODE_UPDATE_REBUILD = 'rebuild';

    /**
     * Options getter
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::MODE_UPDATE_ADD,
                'label' => __('Create new invoice (if possible)')
            ], [
                'value' => self::MODE_UPDATE_REBUILD,
                'label' => __('Delete invoices and credit memos, create new invoice instead')
            ],
        ];
    }
}
