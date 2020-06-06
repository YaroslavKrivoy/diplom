<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use MageWorx\OrderEditor\Model\Quote\Item;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\DataObject;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Sales\Controller\Adminhtml\Order\Create;
use MageWorx\OrderEditor\Helper\Data as Helper;

class Options extends Create
{
    /**
     * @var \Mageworx\OrderEditor\Model\Edit\Quote $quote
     */
    protected $quote;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param \Mageworx\OrderEditor\Model\Edit\Quote $quote
     * @param Helper $helper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        \MageWorx\OrderEditor\Model\Edit\Quote $quote,
        Helper $helper
    ) {
        parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);
        $this->quote = $quote;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $updateResult = new DataObject();

        try {
            $orderItemId = $this->getRequest()->getParam('id');
            $params = $this->getRequest()->getParams();

            $prefixIdLength = strlen(Item::PREFIX_ID);
            if (substr($orderItemId, 0, $prefixIdLength) == Item::PREFIX_ID) {
                $quoteItemId = substr(
                    $orderItemId,
                    $prefixIdLength,
                    strlen($orderItemId)
                );
                $orderItem = $this->quote->getUpdatedOrderItem($quoteItemId, $params);
            } else {
                $orderItem = $this->quote->createNewOrderItem($orderItemId, $params);
                $orderItem->setId($orderItemId);
            }

            $resultPage = $this->resultPageFactory->create();
            /** @var \Mageworx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Options $optionsBlock */
            $optionsBlock = $resultPage->getLayout()
                ->getBlock('ordereditor_order_edit_form_items_options');
            if (!empty($optionsBlock)) {
                $optionsHtml = $optionsBlock
                    ->setOrderItem($orderItem)
                    ->toHtml();

                $updateResult->setOptionsHtml($optionsHtml);
            }

            $productOptions = $orderItem->getData('product_options');
            $options = $this->helper->encodeBuyRequestValue($productOptions);

            $updateResult->setProductOptions($options);

            $updateResult->setPrice($orderItem->getData('base_price'));
            $updateResult->setName($orderItem->getData('name'));
            $updateResult->setSku($orderItem->getData('sku'));
            $updateResult->setItemId($orderItemId);

            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $updateResult->setError(true);
            $updateResult->setMessage($errorMessage);
        }

        $jsVarName = $this->getRequest()->getParam('as_js_varname');
        $updateResult->setJsVarName($jsVarName);

        $this->_objectManager->get('Magento\Backend\Model\Session')
            ->setCompositeProductResult($updateResult);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('catalog/product/showUpdateResult');
    }
}
