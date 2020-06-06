<?php
namespace Webfitters\HearAbout\Model\Resource;

class HearAbout extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('wf_hear_abouts', 'id');
    }

}