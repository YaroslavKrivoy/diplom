<?php
namespace Webfitters\HearAbout\Ui\Component\Listing\Columns;

class HearAboutActions extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $url;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $component,
        \Magento\Framework\UrlInterface $url,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $component, $components, $data);
        $this->url = $url;
    }

    public function prepareDataSource(array $source) {
        if (isset($source['data']['items'])) {
            foreach ($source['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->url->getUrl('hearabout/hearabout/edit', ['id' => $item['id'], 'store' => $this->context->getFilterParam('store_id')]),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->url->getUrl('hearabout/hearabout/delete', ['id' => $item['id'], 'store' => $this->context->getFilterParam('store_id')]),
                    'label' => __('Delete'),
                    'hidden' => false,
                ];
            }
        }
        return $source;
    }

}