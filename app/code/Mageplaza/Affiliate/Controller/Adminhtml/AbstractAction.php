<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 *                     @category  Mageplaza
 *                     @package   Mageplaza_Affiliate
 *                     @copyright Copyright (c) 2016
 *                     @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Controller\Adminhtml;

abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @type \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @type \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry          = $coreRegistry;
        $this->_resultPageFactory          = $resultPageFactory;
        parent::__construct($context);
    }
}
