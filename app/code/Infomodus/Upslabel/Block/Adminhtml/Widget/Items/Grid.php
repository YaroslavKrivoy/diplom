<?php

/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 30.01.2016
 * Time: 23:04
 */
namespace Infomodus\Upslabel\Block\Adminhtml\Widget\Items;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            $this->getCollection()->addFieldToFilter('lstatus', 0);
        }
        return parent::_prepareCollection();
    }

    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }
}
