<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Block\Adminhtml\Grid;

class PrintingHelper extends \Magento\Backend\Block\Template
{
    /**
     * PrintingHelper constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data)
    {
        parent::__construct($context, $data);
    }

    /**
     * JSON config for the printingHelper
     *
     * @return string
     */
    public function getJsConfig()
    {
        $config = [
            'print_url' => $this->getUrl('mageworx_ordersgrid/order_grid/printPdf/')
        ];

        return json_encode($config);
    }
}
