<?php
namespace Webfitters\PdfPrint\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class InvoicePrinted extends Column {

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
                if(isset($item[$this->getData('name')]) && $item[$this->getData('name')] == '1'){
                    $item[$this->getData('name')] = 'Yes';
                } else {
                    $item[$this->getData('name')] = 'No';
                }  
            }
        }
        return $dataSource;
    }

}