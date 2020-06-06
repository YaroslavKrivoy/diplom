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
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Block\Adminhtml\Transaction\View;

/**
 * Class Tabs
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction\View
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
	/**
	 * constructor
	 *
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->setId('transaction_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(__('Transaction Information'));
	}
}
