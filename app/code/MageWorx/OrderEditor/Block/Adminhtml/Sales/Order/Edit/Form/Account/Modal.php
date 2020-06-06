<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Account;

use Magento\Backend\Block\Template;

class Modal extends Template
{
    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getTitle()
    {
        return $this->getData('title') ? $this->getData('title') : '';
    }
}
