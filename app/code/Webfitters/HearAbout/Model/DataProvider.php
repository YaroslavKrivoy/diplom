<?php
namespace Webfitters\HearAbout\Model;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    public function __construct(
        $name,
        $primary,
        $request,
        \Webfitters\HearAbout\Model\Resource\HearAbout\CollectionFactory $hearAbout,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $hearAbout->create();
        parent::__construct($name, $primary, $request, $meta, $data);
    }

    public function getData() {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        $this->loadedData = array();
        foreach ($items as $source) {
            $this->loadedData[$source->getId()]['hear_about'] = $source->getData();
        }
        return $this->loadedData;
    }

}