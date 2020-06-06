<?php
namespace Webfitters\Pdf\Controller;

class Router implements \Magento\Framework\App\RouterInterface {

    protected $actionFactory;
    protected $_response;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }
 
    public function match(\Magento\Framework\App\RequestInterface $request) {
        $identifier = trim($request->getPathInfo(), '/');
        if(strpos($identifier, 'pdf/download') !== false && $request->getActionName() != 'noroute') {
            $request->setActionName('index')->setParam('pdf', str_replace('pdf/download/', '', $identifier));
        } else {  
            return;
        }
    }

}