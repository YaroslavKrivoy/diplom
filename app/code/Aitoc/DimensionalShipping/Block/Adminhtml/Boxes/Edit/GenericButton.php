<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Block\Adminhtml\Boxes\Edit;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Supplier\Edit
 */
class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context                    $context
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->context    = $context;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry   = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Return the current Supplier Id.
     *
     * @return int|null
     */
    public function getBoxId()
    {
        $supplier = $this->registry->registry('entity_box');

        return $supplier ? $supplier->getItemId() : null;
    }
}
