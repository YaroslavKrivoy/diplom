<?php
namespace Webfitters\Hero\Model\Page;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Cms\Model\Page\DataProvider {

	protected $filesystem;

    public function __construct(
    	string $name,
        string $primaryFieldName,
        string $requestFieldName,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Filesystem $filesystem,
        array $meta = [],
        array $data = []
    ){
    	$this->filesystem=$filesystem;
    	parent::__construct($name, $primaryFieldName, $requestFieldName, $pageCollectionFactory, $dataPersistor, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $page \Magento\Cms\Model\Page */
        foreach ($items as $page) {
            $this->loadedData[$page->getId()] = $page->getData();
        }

        $data = $this->dataPersistor->get('cms_page');


        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();

            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('cms_page');
            if(!empty($this->loadedData[$page->getId()]['hero_image'])){
                $media = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                $currentStore = $storeManager->getStore();
                $media_url=$currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $image_name=$this->loadedData[$page->getId()]['hero_image'];
                if(!is_array($image_name)){
                    unset($this->loadedData[$page->getId()]['hero_image']);
                    $this->loadedData[$page->getId()]['hero_image'][0]['name'] = basename($image_name);
                    $this->loadedData[$page->getId()]['hero_image'][0]['url'] = $media_url.$image_name;
                    $this->loadedData[$page->getId()]['hero_image'][0]['size'] = filesize($media.$image_name);
                }
            }
        }
        
        return $this->loadedData;
    }
}