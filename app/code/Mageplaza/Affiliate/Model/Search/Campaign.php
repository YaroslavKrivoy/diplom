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
 *                     @category  Mageplaza
 *                     @package   Mageplaza_Affiliate
 *                     @copyright Copyright (c) 2016
 *                     @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Model\Search;

class Campaign extends \Magento\Framework\DataObject
{
    /**
     * Campaign Collection factory
     * 
     * @var \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Backend data helper
     * 
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData;

    /**
     * constructor
     * 
     * @param \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     */
    public function __construct(
        \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $adminhtmlData
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_adminhtmlData     = $adminhtmlData;
        parent::__construct();
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $query = $this->getQuery();
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('name', ['like' => '%'.$query.'%'])
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $campaign) {
            $result[] = [
                'id' => 'mageplaza_affiliate_campaign/1/' . $campaign->getId(),
                'type' => __('Affiliate Campaign'),
                'name' => $campaign->getName(),
                'description' => $campaign->getDescription(),
                'form_panel_title' => __(
                    'Campaign %1',
                    $campaign->getName()
                ),
                'url' => $this->_adminhtmlData->getUrl('mageplaza_affiliate/campaign/edit', ['campaign_id' => $campaign->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
