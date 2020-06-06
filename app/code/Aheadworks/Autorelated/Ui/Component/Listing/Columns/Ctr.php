<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Ctr
 * @package Aheadworks\Autorelated\Ui\Component\Listing\Columns
 */
class Ctr extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as & $item) {
            $fieldName = $this->getData('name');
            $item[$fieldName] = (int)$item[$fieldName] . '%';
        }
        return $dataSource;
    }
}
