<?php

namespace KozakGroup\RewriteOrderEditor\Block\Adminhtml;


class EditOrderButton extends \Magento\Backend\Block\Widget\Container
{
    protected $buttonList;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->updateButton('order_edit', 'class', 'edit');
        $this->updateButton('order_edit','sort_order', 900);
        $this->updateButton('fooman_print', 'class', 'print');
        $this->updateButton('fooman_print','label','Print Order');
        $this->updateButton('fooman_print','sort_order',910);
        $this->addButton('print_label',
            [
            'label' => __('Create & Print Label'),
            'class' => 'print primary',
            'onclick' => ''
        ],
            -100);
    }
}
