<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\Api\Filter;

/**
 * Class RuleDataProvider
 *
 * @package Aheadworks\Autorelated\Ui\DataProvider
 */
class RuleDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $repository;

    /**
     * @var SearchCriteria
     */
    private $searchCriteria;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RuleRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RuleRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectProcessor $dataObjectProcessor,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_autorelated_rule');
        if (!empty($dataFromForm)) {
            $data[$dataFromForm['id']] = $dataFromForm;
            $this->dataPersistor->clear('aw_autorelated_rule');
        } else {
            $searchResult = $this->getSearchResult();
            foreach ($searchResult->getItems() as $item) {
                $itemData = $this->dataObjectProcessor->buildOutputDataArray(
                    $item,
                    RuleInterface::class
                );

                $data[$itemData['id']] = $itemData;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        $this->searchCriteriaBuilder->addFilters([$filter]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        return $this->repository->getList($this->getSearchCriteria());
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
        }
        return $this->searchCriteria;
    }
}
