<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 06.11.19
 * Time: 17:24
 */

namespace KozakGroup\RewriteOrderEditor\Block\Adminhtml\Label\Edit;


class ButtonList extends \Magento\Backend\Block\Widget\Container
{
    protected $buttonList;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->updateButton('save', 'label', 'Save & Generate Label');
    }

}