<?php
namespace Webfitters\Wholesale\Controller;

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
        if(strpos($identifier, 'wholesaler/type') !== false && $request->getActionName() != 'noroute') {
            $request->setActionName('index')->setParam('type', str_replace('wholesaler/type/', '', $identifier));
        } else {  
            return;
        }
    }

}