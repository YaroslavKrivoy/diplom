<?php
namespace Webfitters\Pdf\Controller\Adminhtml\Pdf;

class Upload extends \Magento\Backend\App\Action {

	protected $directory;
    protected $pdf;
    protected $store;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory,
        \Webfitters\Pdf\Model\PdfFactory $pdf,
        \Magento\Store\Model\StoreManagerInterface $store
    ) {
         parent::__construct($context);
         $this->directory = $directory;
         $this->pdf = $pdf;
         $this->store = $store;
    }

    public function execute() {
    	if($this->getRequest()->isAjax()){
    		$file = (object)$this->getRequest()->getFiles('pdf');
    		if(!$file){
    			echo json_encode(array('error' => true, 'message' => 'No file uploaded'));
    			die();
    		}
    		if($file->type != 'application/pdf'){
    			echo json_encode(array('error' => true, 'message' => 'File uploaded must be a pdf'));
    			die();
    		}
    		$media = $this->directory->getPath('media');
    		if(!file_exists($media.'/webfitters')){
    			mkdir($media.'/webfitters');
    		}
    		if(!file_exists($media.'/webfitters/pdf')){
    			mkdir($media.'/webfitters/pdf');
    		}
    		$name = md5(microtime()).'.pdf';
    		rename($file->tmp_name, $media.'/webfitters/pdf/'.$name);
            $pdf = $this->pdf->create();
            $pdf->setFile($name);
            $pdf->setLink($name);
            $pdf->setCreatedAt(date('Y-m-d H:i:s'));
            $pdf->setUpdatedAt(date('Y-m-d H:i:s'));
            $pdf->save();
            $png = new \Imagick();
            $png->setResolution(300, 300);
            $png->readImage($media.'/webfitters/pdf/'.$name.'[0]');
            $png->resizeImage(500, 0, \Imagick::FILTER_LANCZOS, 1);
            $png->setImageFormat('png');
            $png->writeImage($media.'/webfitters/pdf/'.$name.'.png');
            $png->clear();
            $png = null;
            echo json_encode(array('error' => false, 'message' => 'PDF successfully uploaded', 'pdf' => array(
                'link' => $pdf->getLink(),
                'id' => $pdf->getId(),
                'image' => $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/pdf/'.$pdf->getFile().'.png',
                'base' => $this->store->getStore()->getBaseUrl(),
                'created_at' => date('m/d/Y', strtotime($pdf->getCreatedAt())),
                'edit_url' => $this->store->getStore()->getUrl('pdf/pdf/edit'),
                'delete_url' => $this->store->getStore()->getUrl('pdf/pdf/delete')

            )));
    		die();
        }
        return false;
    }
}