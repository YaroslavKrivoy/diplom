<?php
namespace Webfitters\Custom\Model\Template;

class Filter extends \Magento\Email\Model\Template\Filter {

	 public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Variable\Model\VariableFactory $coreVariableFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\UrlInterface $urlModel,
        \Pelago\Emogrifier $emogrifier,
        \Magento\Email\Model\Source\Variables $configVariables,
        $variables = [],
        \Magento\Framework\Css\PreProcessor\Adapter\CssInliner $cssInliner = null
    ) {
    	$this->_modifiers['date'] = function($value, $format){
            try {
        		return \Carbon\Carbon::parse($value)->format($format);
            } catch(\Exception $e){
                return '';
            }
    	};
    	parent::__construct(
    		$string, 
    		$logger, 
    		$escaper, 
    		$assetRepo, 
    		$scopeConfig, 
    		$coreVariableFactory, 
    		$storeManager, 
    		$layout, 
    		$layoutFactory, 
    		$appState, 
    		$urlModel, 
    		$emogrifier, 
    		$configVariables, 
    		$variables, 
    		$cssInliner
    	);
    }

}