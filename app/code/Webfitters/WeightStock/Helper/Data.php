<?php
namespace Webfitters\WeightStock\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $config;
	protected $directory;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Framework\App\Filesystem\DirectoryList $directory
	) {
		parent::__construct($context);
		$this->config = $config;
		$this->directory = $directory;
	}

	public function getLogoPath(){
		$folder = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $logo = $this->config->getValue('design/header/logo_src', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $path = $this->directory->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA).'/'.$folder.'/'.$logo;
        return $path;
	}

}