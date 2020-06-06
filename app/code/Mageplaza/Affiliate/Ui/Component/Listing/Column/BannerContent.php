<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class BannerContent extends \Magento\Ui\Component\Listing\Columns\Column
{
	protected $filterProvider;

	/**
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
	 * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
	 * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		array $components = [],
		array $data = []
	)
	{
		parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->filterProvider = $filterProvider;
	}

	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */

	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as & $item) {
				$item['content_html'] = $this->filterProvider->getPageFilter()->filter($item['content']);
			}
		}

		return $dataSource;
	}
}
