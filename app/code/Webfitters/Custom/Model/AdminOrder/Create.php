<?php
namespace Webfitters\Custom\Model\AdminOrder;

class Create extends \Magento\Sales\Model\AdminOrder\Create {

	public function createOrder() {
        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();

        $orderData = [];
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId();
            if (!$originalId) {
                $originalId = $oldOrder->getIncrementId();
            }
            $orderData = [
                'original_increment_id' => $originalId,
                'relation_parent_id' => $oldOrder->getId(),
                'relation_parent_real_id' => $oldOrder->getIncrementId(),
                'edit_increment' => $oldOrder->getEditIncrement() + 1,
                'increment_id' => $originalId . '-' . ($oldOrder->getEditIncrement() + 1)
            ];
            $quote->setReservedOrderId($orderData['increment_id']);
        }

        $order = $this->quoteManagement->submit($quote, $orderData);

        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $oldOrder->setRelationChildId($order->getId());
            $oldOrder->setRelationChildRealId($order->getIncrementId());
            $oldOrder->save();
            $this->orderManagement->cancel($oldOrder->getEntityId());
            $order->save();
        }
        $this->_eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
        if ($this->getSendConfirmation()) {
            $this->emailSender->send($order);
        }
        return $order;
    }

}