<?php

namespace Nwdthemes\Revslider\Model\Template;

class Filter extends \Magento\Widget\Model\Template\Filter {

	protected $_scopeConfig;
	protected $_layout;

	/**
	 *	Constructor
	 */
        
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
        \Magento\Widget\Model\ResourceModel\Widget $widgetResource,
        \Magento\Widget\Model\Widget $widget
    ) {
		$this->_scopeConfig = $scopeConfig;
		$this->_layout = $layout;
		
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
			$widgetResource,
			$widget
		);
	}

    /**
     * Generate Slider Revolution
     *
     * @param string[] $construction
     * @return string
     */
    public function revsliderDirective($construction) {
		if ( $this->_scopeConfig->getValue('nwdthemes_revslider/revslider_configuration/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ) {
			$params = $this->getParameters($construction[2]);
			$alias = isset($params['alias']) ? $params['alias'] : '';
			$sliderBlock = $this->_layout->createBlock('Nwdthemes\Revslider\Block\Revslider', 'revslider-' . $alias);
			$sliderBlock->setData('alias', $alias);
			$output = $sliderBlock->toHtml();
		} else {
			$output = '';
		}
		return $output;
	}

}