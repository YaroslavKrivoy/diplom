<?php 
namespace Emipro\CustomNewsletterSender\Controller\Adminhtml\Newsletter\Queue;
class Save extends \Magento\Newsletter\Controller\Adminhtml\Queue\Save {
    public function execute(){
    	try {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $queue = $objectManager->create('Magento\Newsletter\Model\Queue');
        $templateId = $this->getRequest()->getParam('template_id');
        $customer_group = $this->getRequest()->getParam('customergroup');
        if ($templateId) {
         $template = $this->_objectManager->create('Magento\Newsletter\Model\Template')->load($templateId);
         	  if (!$template->getId() || $template->getIsSystem()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please correct the newsletter template and try again.'));
                }

               $queue->setTemplateId(
                    $template->getId()
                )->setQueueStatus(
                    \Magento\Newsletter\Model\Queue::STATUS_NEVER
                );         
            } else {
                $queue->load($this->getRequest()->getParam('id'));
                echo "<pre>";
               
            }
              if (!in_array(
                $queue->getQueueStatus(),
                [\Magento\Newsletter\Model\Queue::STATUS_NEVER, \Magento\Newsletter\Model\Queue::STATUS_PAUSE]
            )
            ) {
                $this->_redirect('*/*');
                return;
            }
            if ($queue->getQueueStatus() == \Magento\Newsletter\Model\Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }
            $queue->setStores(
                $this->getRequest()->getParam('stores', [])
            )->setNewsletterSubject(
                $this->getRequest()->getParam('subject')
            )->setNewsletterSenderName(
                $this->getRequest()->getParam('sender_name')
            )->setNewsletterSenderEmail(
                $this->getRequest()->getParam('sender_email')
            )->setNewsletterText(
                $this->getRequest()->getParam('text')
            )->setNewsletterStyles(
                $this->getRequest()->getParam('styles')
            );
            if ($queue->getQueueStatus() == \Magento\Newsletter\Model\Queue::STATUS_PAUSE
                && $this->getRequest()->getParam(
                    '_resume',
                    false
                )
            ) {
                $queue->setQueueStatus(\Magento\Newsletter\Model\Queue::STATUS_SENDING);
            }
            $queue->setData('customer_group',$customer_group);
            $queue->save();
            $this->_redirect('*/*');
        }catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            }
        }
    }
}