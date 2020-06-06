<?php
namespace Webfitters\Pdf\Controller\Adminhtml\Pdf;

class Edit extends \Magento\Backend\App\Action {

	protected $pdf;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webfitters\Pdf\Model\PdfFactory $pdf
    ) {
         parent::__construct($context);
         $this->pdf = $pdf;
    }

    public function execute() {
    	$pdf = $this->pdf->create()->load($this->getRequest()->getParam('id'), 'id');
    	if(!$pdf->getId()){
    		echo json_encode(array('error' => true, 'message' => 'Couldn\'t find pdf.'));
    		die();
    	}
    	$pdf->setLink($this->getRequest()->getParam('link'));
    	$pdf->save();
    	echo json_encode(array('error' => false, 'message' => 'PDF successfully saved.'));
        die();
    }
}