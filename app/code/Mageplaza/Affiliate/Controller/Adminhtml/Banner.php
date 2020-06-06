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
namespace Mageplaza\Affiliate\Controller\Adminhtml;

/**
 * Class Banner
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Banner extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * Init Banner
	 *
	 * @return \Mageplaza\Affiliate\Model\Banner
	 */
	protected function _initBanner()
	{
		$bannerId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Banner $banner */
		$banner = $this->_objectManager->create('Mageplaza\Affiliate\Model\Banner');
		if ($bannerId) {
			$banner->load($bannerId);
		}
		if (!$this->_coreRegistry->registry('current_banner')) {
			$this->_coreRegistry->register('current_banner', $banner);
		}

		return $banner;
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::banner');
	}
}
