<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Types;

class Save extends \Magento\Backend\App\Action {

    protected $types;
    protected $directory;
    protected $store;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webfitters\Wholesale\Model\TypeFactory $types,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\App\Filesystem\DirectoryList $directory
    ) {
         parent::__construct($context);
         $this->types = $types;
         $this->directory = $directory;
         $this->store = $store;
    }

    public function getImageUrl($file){
        return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/wholesale/'.$file;
    }

    public function execute() {
        if(!$this->getRequest()->isAjax()){
            return false;
        }
        if($this->getRequest()->getParam('id') && $this->getRequest()->getParam('id') != ''){
            $type = $this->types->create()->load($this->getRequest()->getParam('id'), 'id');
        } else {
            $type = $this->types->create();
        }
        $type->setTitle($this->getRequest()->getParam('title'));
        $type->setContent($this->getRequest()->getParam('content'));
        $media = $this->directory->getPath('media');
        $image = (object)$this->getRequest()->getFiles('image');
        $hero = (object)$this->getRequest()->getFiles('hero_image');
        if(!file_exists($media.'/webfitters')){
            mkdir($media.'/webfitters');
        }
        if(!file_exists($media.'/webfitters/wholesale')){
            mkdir($media.'/webfitters/wholesale');
        }
        if($image && isset($image->name)){
            $ext = substr($image->name, strrpos($image->name, '.') + 1, strlen($image->name) - strrpos($image->name, '.') - 1);
            $name = md5(microtime()).'.'.$ext;
            rename($image->tmp_name, $media.'/webfitters/wholesale/'.$name);
            chmod($media.'/webfitters/wholesale/'.$name, 0644);
            $type->setImage($name);
        }
        if($hero && isset($hero->name)){
            $ext = substr($hero->name, strrpos($hero->name, '.') + 1, strlen($hero->name) - strrpos($hero->name, '.') - 1);
            $name = md5(microtime()).'_hero.'.$ext;
            rename($hero->tmp_name, $media.'/webfitters/wholesale/'.$name);
            chmod($media.'/webfitters/wholesale/'.$name, 0644);
            $type->setHero($name);
        }
        $type->save();
        echo json_encode(array('error' => 'false', 'message' => 'Type successfully created.', 'type' => array(
            'image' => $this->getImageUrl($type->getImage()),
            'content' => $type->getContent(),
            'hero' => $this->getImageUrl($type->getHero()),
            'title' => $type->getTitle(),
            'id' => $type->getId()
        )));
        die();
    }
}