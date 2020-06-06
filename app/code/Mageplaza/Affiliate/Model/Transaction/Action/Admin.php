<?php
namespace Mageplaza\Affiliate\Model\Transaction\Action;

use \Mageplaza\Affiliate\Model\Transaction\AbstractAction;
use \Mageplaza\Affiliate\Model\Transaction\Status;
use \Mageplaza\Affiliate\Model\Transaction\Type;

class Admin extends AbstractAction
{
	public function getAmount()
	{
		return $this->getObject()->getAmount();
	}

	public function getType(){
		return Type::ADMIN;
	}

	public function getStatus(){
		$holdDays = $this->getObject()->getHoldDay();
		if($holdDays && $holdDays > 0){
			return Status::STATUS_HOLD;
		}

		return Status::STATUS_COMPLETED;
	}

	public function getTitle($transaction = null)
	{
		$object = is_null($transaction) ? $this->getObject() : $transaction;

		return $object->getTitle() ?: __('Changed by Admin');
	}

	public function prepareAction()
	{
		$actionObject = $this->getObject();
		if($holdDay = $actionObject->getHoldDay()){
			return array('holding_to' => $this->getHoldingDate($holdDay));
		}

		return array();
	}
}