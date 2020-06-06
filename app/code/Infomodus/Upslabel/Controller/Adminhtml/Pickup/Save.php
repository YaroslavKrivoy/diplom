<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pickup;

class Save extends \Infomodus\Upslabel\Controller\Adminhtml\Pickup
{
    protected $handy;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->handy = $handy;
    }
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data= [];
            try {
                $model = $this->_objectManager->create('Infomodus\Upslabel\Model\Pickup');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                $data['CloseTime'] = implode(",", $data['CloseTime']);
                $data['ReadyTime'] = implode(",", $data['ReadyTime']);
                $data['PickupDateYear'] = $data['PickupDateYear'] == "0" ? date("Y") : $data['PickupDateYear'];
                $data['PickupDateMonth'] = $data['PickupDateMonth'] == "0" ? date("m") : $data['PickupDateMonth'];
                $data['PickupDateDay'] = $data['PickupDateDay'] == "0" ? date("d") : $data['PickupDateDay'];
                if (isset($data['oadress']['OtherAddress']) && $data['oadress']['OtherAddress']==1) {
                    $data['ShipFrom'] = json_encode($data['oadress']);
                }
                $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('Y-m-d H:i:s'))
                        ->setUpdateTime(date('Y-m-d H:i:s'));
                } else {
                    $model->setUpdateTime(date('Y-m-d H:i:s'));
                }

                $this->handy->setPickup($data);

                if ($this->getRequest()->getParam('id') < 1) {
                    $price = $this->handy->pickup->ratePickup();
                    $response = $this->handy->pickup->getPickup();
                } else {
                    $this->handy->cancelPickup($this->getRequest()->getParam('id'));
                    $price = $this->handy->pickup->ratePickup();
                    $response = $this->handy->pickup->getPickup();
                }
                if (!isset($response['error'])) {
                    if ($this->getRequest()->getParam('id') < 1) {
                        $model->setData('pickup_request', $response['data']);
                        $model->setData('pickup_response', $response['response']);
                        $model->setData('status', $response['Description']);
                        $model->setData('price', $price);
                        $this->messageManager->addSuccessMessage(__('Pickup was successfully saved'));
                    } else {
                        $model->setData('pickup_request', $response['data']);
                        $model->setData('pickup_response', $response['response']);
                        $model->setData('status', $response['Description']);
                        $model->setData('price', $price);
                        $this->messageManager->addSuccessMessage(__('Pickup was successfully modified'));
                    }

                    $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                    $session->setPageData($model->getData());
                    $model->save();
                    $session->setPageData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                        return;
                    }

                    $this->_redirect('*/*/');
                    return;
                } else {
                    $this->getResponse()
                        ->setContent($response['error']);
                    return;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('infomodus_upslabel/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('infomodus_upslabel/*/new');
                }

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('infomodus_upslabel/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }

        $this->_redirect('infomodus_upslabel/*/');
    }
}
