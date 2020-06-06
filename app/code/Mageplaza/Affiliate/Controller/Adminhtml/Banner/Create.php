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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Create
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Banner
 */
class Create extends \Mageplaza\Affiliate\Controller\Adminhtml\Banner
{
	/**
	 * Create new banner
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->_forward('edit');
	}
}
