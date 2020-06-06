<?php
namespace Webfitters\HearAbout\Block\Adminhtml\HearAbout\Edit;

class GenericButton {

    protected $urlBuilder;
    protected $registry;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    public function getId() {
        $hear_about = $this->registry->registry('hear_about');
        return $hear_about ? $hear_about->getId() : null;
    }

    public function getUrl($route = '', $params = []) {
        return $this->urlBuilder->getUrl($route, $params);
    }

}