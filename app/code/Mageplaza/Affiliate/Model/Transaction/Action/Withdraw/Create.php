<?php
namespace Mageplaza\Affiliate\Model\Transaction\Action\Withdraw;

use Mageplaza\Affiliate\Model\Transaction\Type;

class Create extends \Mageplaza\Affiliate\Model\Transaction\AbstractAction
{
	public function getAmount()
	{
		return -(float)$this->getObject()->getAmount();
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return Type::PAID;
	}

	public function getTitle($transaction = null)
	{
		return __('Subtract balance for withdraw request');
	}
}