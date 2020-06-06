<?php
namespace Emipro\CustomNewsletterSender\Model\ResourceModel\Queue\Grid;

class Collection extends \Emipro\CustomNewsletterSender\Model\ResourceModel\Queue\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscribersInfo();
        return $this;
    }
}
