<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Listing\Columns;

use Aheadworks\Autorelated\Model\Source\Status;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Aheadworks\Autorelated\Ui\Component\Listing\Columns
 */
class Actions extends Column
{
    /**#@+
     * Constants for url path
     */
    const URL_PATH_EDIT = 'autorelated_admin/rule/edit';
    const URL_PATH_DELETE = 'autorelated_admin/rule/delete';
    const URL_PATH_CHANGE_STATUS = 'autorelated_admin/rule/changeStatus';
    /**#@-*/

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $title = $item['code'];
            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_EDIT,
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'label' => __('Edit')
                ],
                'change_status' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_CHANGE_STATUS,
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'label' => $item['status'] == Status::STATUS_ENABLED ? __('Disable') : __('Enable')
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_DELETE,
                        [
                            'id' => $item['id'],
                            'back' => 'listing'
                        ]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete %1', $title),
                        'message' => __('Are you sure you want to delete a %1 record?', $title)
                    ]
                ]
            ];
        }
        return $dataSource;
    }
}
