<?php
namespace Webfitters\ShippingNotes\Model;

class CheckoutConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface {

    protected $block;

    public function __construct(
        \Magento\Cms\Block\Block $block, 
        $blockId
    ) {
        $this->block = $block;
        $block->setData('block_id', $blockId);
        /*$block->setTemplate('Magento_Cms::widget/static_block/default.phtml');*/
    }

    public function getConfig()
    {
        return [
            'shippingHtml' => $this->block->toHtml()
        ];
    }
}