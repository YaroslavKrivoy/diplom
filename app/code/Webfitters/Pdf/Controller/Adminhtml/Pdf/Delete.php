<?php
namespace Webfitters\Pdf\Controller\Adminhtml\Pdf;

class Delete extends \Magento\Backend\App\Action {

	protected $pdf;
	protected $directory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory,
        \Webfitters\Pdf\Model\PdfFactory $pdf
    ) {
         parent::__construct($context);
         $this->pdf = $pdf;
         $this->directory = $directory;
    }

    public function execute() {
    	$pdf = $this->pdf->create()->load($this->getRequest()->getParam('id'), 'id');
    	if(!$pdf->getId()){
    		echo json_encode(array('error' => true, 'message' => 'PDF not found.'));
    		die();
    	}
    	$media = $this->directory->getPath('media');
    	if(file_exists($media.'/webfitters/pdf/'.$pdf->getFile())){
    		unlink($media.'/webfitters/pdf/'.$pdf->getFile());
    	}
    	if(file_exists($media.'/webfitters/pdf/'.$pdf->getFile().'.png')){
    		unlink($media.'/webfitters/pdf/'.$pdf->getFile().'.png');
    	}
    	$pdf->delete();
        echo json_encode(array('error' => false, 'message' => 'PDF deleted.'));
        die();
    }
}