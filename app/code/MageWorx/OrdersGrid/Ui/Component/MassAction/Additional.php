<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Ui\Component\MassAction;

use Zend\Stdlib\JsonSerializable;
use Magento\Framework\UrlInterface;

class Additional implements JsonSerializable
{
    const URL_PATH = 'mageworx_ordersgrid/order_grid/';
    const TYPE_COMPLETE = 'complete';
    const TYPE_DELETE_COMPLETELY = 'deleteCompletely';

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Sub-actions Base URL
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Sub-actions additional params
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(UrlInterface $urlBuilder, array $data = [])
    {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if (empty($this->options)) {
            $this->prepareOptionsData();

            $this->options[static::TYPE_COMPLETE] = array_merge_recursive(
                $this->options[static::TYPE_COMPLETE] = [
                    'type' => static::TYPE_COMPLETE,
                    'label' => __('Complete'),
                    'url' => $this->urlBuilder->getUrl(static::URL_PATH . static::TYPE_COMPLETE, []),
                    'confirm' => [
                        'title' => __('Complete'),
                        'message' => __('Are you sure you want to complete selected items?')
                    ]
                ],
                $this->additionalData
            );

            $this->options[static::TYPE_DELETE_COMPLETELY] = array_merge_recursive(
                $this->options[static::TYPE_DELETE_COMPLETELY] = [
                    'type' => static::TYPE_DELETE_COMPLETELY,
                    'label' => __('Delete Completely'),
                    'url' => $this->urlBuilder->getUrl(static::URL_PATH . static::TYPE_DELETE_COMPLETELY, []),
                    'confirm' => [
                        'title' => __('Delete Completely'),
                        'message' => __('Are you sure you want to Delete Completely selected items?')
                    ]
                ],
                $this->additionalData
            );

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare sub-actions addition data
     *
     * @return void
     */
    protected function prepareOptionsData()
    {
        foreach ($this->data as $dataKey => $dataValue) {
            switch ($dataKey) {
                case 'urlPath':
                    $this->urlPath = $dataValue;
                    break;
                default:
                    $this->additionalData[$dataKey] = $dataValue;
                    break;
            }
        }
    }
}
