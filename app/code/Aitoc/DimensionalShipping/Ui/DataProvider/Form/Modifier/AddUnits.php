<?php

namespace Aitoc\DimensionalShipping\Ui\DataProvider\Form\Modifier;

use Aitoc\DimensionalShipping\Helper\Data;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Data provider for main panel of product page
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddUnits extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    private $helper;

    private $scopeConfigInterface;

    private $request;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager     $arrayManager
     */
    public function __construct(
        Data $helper,
        ScopeConfigInterface $scopeConfigInterface,
        RequestInterface $request,
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->helper               = $helper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->locator              = $locator;
        $this->arrayManager         = $arrayManager;
        $this->request              = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        //UNIT TYPE FUNCTIONALITY
        /*if ($this->request->getParam('id')) {
            $box  = $this->helper->getBoxById($this->request->getParam('id'));
            $unit = $box->getUnit();
        } else {*/
        $unit = $this->helper->getGeneralConfig('unit');
        //}

        $fields = $this->helper->getBoxModelFields('long');
        foreach ($fields as $field) {
            $meta['general']['children'][$field]['arguments']['data']['config']['addafter'] = $unit;
        }
        $fields = $this->helper->getBoxModelFields('weight');
        foreach ($fields as $field) {
            $meta['general']['children'][$field]['arguments']['data']['config']['addafter'] = $this->scopeConfigInterface->getValue(
                'general/locale/weight_unit'
            );
        }

        return $meta;
    }
}
