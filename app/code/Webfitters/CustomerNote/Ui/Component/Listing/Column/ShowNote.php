<?php
namespace Webfitters\CustomerNote\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ShowNote extends Column {

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if($item[$this->getData('name')] && $item[$this->getData('name')] != ''){
                    $item[$this->getData('name')] = '<i class="fa fa-fw fa-exclamation-circle"></i>';
                } else {
                    $item[$this->getData('name')] = '';
                }  
            }
        }
        return $dataSource;
    }

}