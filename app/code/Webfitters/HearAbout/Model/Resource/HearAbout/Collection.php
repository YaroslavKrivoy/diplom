<?php
namespace Webfitters\HearAbout\Model\Resource\HearAbout;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Webfitters\HearAbout\Model\HearAbout', 'Webfitters\HearAbout\Model\Resource\HearAbout');
    }

}