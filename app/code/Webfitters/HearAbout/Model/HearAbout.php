<?php
namespace Webfitters\HearAbout\Model;

class HearAbout extends \Magento\Framework\Model\AbstractModel {

    protected function _construct() {
        $this->_init('Webfitters\HearAbout\Model\Resource\HearAbout');
    }

}