<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Observer;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\AstoundAffirm;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\App\RequestInterface;

/**
 * Class AddCssToAstoundAffirmObserver
 * @package Aheadworks\OneStepCheckout\Observer
 */
class AddCssToAstoundAffirmObserver implements ObserverInterface
{
    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AstoundAffirm
     */
    private $astoundAffirmStatus;

    /**
     * @param PageConfig $pageConfig
     * @param RequestInterface $request
     * @param AstoundAffirm $astoundAffirmStatus
     */
    public function __construct(
        PageConfig $pageConfig,
        RequestInterface $request,
        AstoundAffirm $astoundAffirmStatus
    ) {
        $this->pageConfig = $pageConfig;
        $this->request = $request;
        $this->astoundAffirmStatus = $astoundAffirmStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if ($this->request->getModuleName() == 'onestepcheckout'
            && $this->request->getControllerName() == 'index'
            && $this->request->getActionName() == 'index'
            && $this->astoundAffirmStatus->isEnabled()
        ) {
            $this->pageConfig->addPageAsset('Astound_Affirm::affirm.css');
        }
    }
}
