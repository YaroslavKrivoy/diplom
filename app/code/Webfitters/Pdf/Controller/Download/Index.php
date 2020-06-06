<?php
namespace Webfitters\Pdf\Controller\Download;

class Index extends \Magento\Framework\App\Action\Action {

	protected $pdf;
	protected $directory;
	protected $rawFactory;
	protected $fileFactory;
	protected $forwardFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\RawFactory $rawFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directory,
        \Webfitters\Pdf\Model\PdfFactory $pdf
	) {
		parent::__construct($context);
		$this->directory = $directory;
		$this->pdf = $pdf;
		$this->fileFactory = $fileFactory;
		$this->rawFactory = $rawFactory;
		$this->forwardFactory = $forwardFactory;
	}

	public function execute(){
		$pdf = $this->pdf->create()->load(urldecode($this->_request->getParam('pdf')), 'link');
		if(!$pdf->getId()){
			return $this->notFound();
		}
		try{
            $this->fileFactory->create(basename($pdf->getLink()), ['type' => 'filename', 'value' => 'webfitters/pdf/'.$pdf->getFile()], \Magento\Framework\App\Filesystem\DirectoryList::MEDIA , 'application/octet-stream', '');
        } catch (\Exception $e) {
        	return $this->notFound();
        }
        return $this->rawFactory->create();
	}

	protected function notFound(){
		return $this->forwardFactory->create()->forward('noroute');
	}

}