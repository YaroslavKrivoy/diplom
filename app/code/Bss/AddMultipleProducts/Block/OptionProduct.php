<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AddMultipleProducts
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AddMultipleProducts\Block;

use Magento\Framework\View\Element\Template\Context;
use Bss\AddMultipleProducts\Helper\Data as HelperData;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;

class OptionProduct extends \Magento\Framework\View\Element\Template
{
    protected $helperData;

    protected $productloader;

    protected $catalogProductOptionTypeDate;

    protected $pricingHelper;

    protected $productImageHelper;

    protected $resultPageFactory;

    /**
     * OptionProduct constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\AddMultipleProducts\Helper\Data $helperData
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\Catalog\Model\Product\Option\Type\Date $catalogProductOptionTypeDate
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Helper\Image $productImageHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Catalog\Model\Product\Option\Type\Date $catalogProductOptionTypeDate,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Helper\Image $productImageHelper,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->productloader = $productloader;
        $this->catalogProductOptionTypeDate = $catalogProductOptionTypeDate;
        $this->pricingHelper = $pricingHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->productImageHelper = $productImageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param $product
     * @param bool $configurable
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductOptionsHtml($product, $configurable = false)
    {
        $blockOption = $this->getLayout()->createBlock("Magento\Catalog\Block\Product\View\Options");
        $blockOptionsHtml = null;

        if ($product->getTypeId() == "simple"
            || $product->getTypeId() == "virtual"
            || $product->getTypeId() == "configurable"
            || $product->getTypeId() == "downloadable") {
            $blockOption->setProduct($product);
            $customOptions = \Magento\Framework\App\ObjectManager::getInstance()
                                    ->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
            if ($customOptions) {
                foreach ($blockOption->getOptions() as $_option) {
                    $blockOptionsHtml .= $this->getValuesHtml($_option, $product);
                }
            }
        }

        if ($product->getTypeId() == "bundle") {
            $blockOptionsHtml .= $this->getOptionsBundleProduct($product);
        }
        if ($product->getTypeId()=="downloadable") {
            $blockViewType = $this->getLayout()->createBlock("Magento\Downloadable\Block\Catalog\Product\Links");
            $blockViewType->setProduct($product);
            $blockViewType->setTemplate("Magento_Downloadable::catalog/product/links.phtml");
            $blockOptionsHtml .= $blockViewType->toHtml();
        }

        if ($product->getTypeId()=="configurable" && $configurable) {
            $blockViewType = $this->getLayout()
                                    ->createBlock("Magento\ConfigurableProduct\Block\Product\View\Type\Configurable");
            $blockViewType->setProduct($product);
            $blockViewType->setTemplate("Bss_AddMultipleProducts::product/view/type/options/configurable.phtml");
            $blockOptionsHtml .= $blockViewType->toHtml();
        }
        return '<div class="fieldset" tabindex="0">'.$blockOptionsHtml.'</div>';
    }
    
    // bundle option product

    /**
     * @param $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionsBundleProduct($product)
    {
        $blockOptionsHtml = '';
        $store_id = $this->_storeManager->getStore()->getId();

        $options = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Bundle\Model\Option')
                                   ->getResourceCollection()
                                   ->setProductIdFilter($product->getId())
                                   ->setPositionOrder();
         
        $options->joinValues($store_id);
        $typeInstance = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Bundle\Model\Product\Type');
         
        $_selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
        $blockOptionbl = $this->getLayout()
                                    ->createBlock("Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option");
        $blockOptionbl->setProduct($product);

        $price = $product->getPriceInfo()->getPrice('bundle_option');
        foreach ($options as $_option) {
            if ($_option->getType() == 'checkbox') {
                $blockOptionsHtml .= $this->getBOptionCheckbox($_option, $_selections, $blockOptionbl, $price);
            }
            if ($_option->getType() == 'multi') {
                $blockOptionsHtml .= $this->getBOptionMulti($_option, $_selections, $blockOptionbl, $price);
            }
            if ($_option->getType() == 'radio') {
                $blockOptionsHtml .= $this->getBOptionRadio($_option, $_selections, $blockOptionbl, $price);
            }
            if ($_option->getType() == 'select') {
                $blockOptionsHtml .= $this->getBOptionSelect($_option, $_selections, $blockOptionbl, $price);
            }
        }

        return $blockOptionsHtml;
    }

    /**
     * @param $_option
     * @param $_selections
     * @param $blockOptionbl
     * @param $price
     * @return string
     */
    public function getBOptionCheckbox($_option, $_selections, $blockOptionbl, $price)
    {
        $amount = 0;
        $blockOptionsHtml = '';
        $blockOptionsHtml.='<div class="field option ';
        if ($_option->getRequired()) {
            $blockOptionsHtml.= 'required';
        }
        $blockOptionsHtml.='">';
        $blockOptionsHtml.='<label class="label"><span>'.htmlspecialchars($_option->getTitle()).'</span></label>';
        $blockOptionsHtml.='<div class="control">
                <div class="nested options-list">';
        foreach ($_selections as $_selection) {
            if ($_selection->getOptionId() == $_option->getId()) {
                $blockOptionsHtml.='<div class="field choice">
                                    <input class="bundle-option-'.$_option->getId().
                                    ' checkbox product bundle option change-container-classname" id="bundle-option-'.$_option->getId().'-'.$_selection->getSelectionId().'"
                                           type="checkbox"';
                if ($_option->getRequired()) {
                    $blockOptionsHtml.= 'data-validate="{\'validate-one-required-by-name\':\'input[name^=&quot;bundle_option[' . $_option->getId() . ']&quot;]:checked\'}"';
                }
                $blockOptionsHtml.='name="bundle_option['.$_option->getId().']['.$_selection->getId().']"
                                           data-selector="bundle_option['.$_option->getId().']['.$_selection->getId().']"';
                if ($blockOptionbl->isSelected($_selection)) {
                    $blockOptionsHtml.=' checked="checked"';
                }
                if (!$_selection->isSaleable()) {
                    $blockOptionsHtml.='disabled="disabled"';
                }
                $amount = $price->getOptionSelectionAmount($_selection);
                $blockOptionsHtml.='value="'.$_selection->getSelectionId().'" price="'.$amount.'" />
                                    <label class="label"
                                           for="bundle-option-'.$_option->getId().'-'.$_selection->getSelectionId().'">
                                        <span>'.$blockOptionbl->getSelectionQtyTitlePrice($_selection).'</span>
                                    </label>
                                </div>';
            }
        }
        $blockOptionsHtml.='<div id="bundle-option-'.$_option->getId().'-container"></div>
                </div>
            </div>
        </div>';
        return $blockOptionsHtml;
    }

    /**
     * @param $_option
     * @param $_selections
     * @param $blockOptionbl
     * @param $price
     * @return string
     */
    public function getBOptionMulti($_option, $_selections, $blockOptionbl, $price)
    {
        $amount = 0;
        $blockOptionsHtml = '';
        $blockOptionsHtml.='<div class="field option ';
        if ($_option->getRequired()) {
            $blockOptionsHtml.= 'required';
        }
        $blockOptionsHtml.='">';
        $blockOptionsHtml.='<label class="label"><span>'.htmlspecialchars($_option->getTitle()).'</span></label>';
        $blockOptionsHtml.='<div class="control">';

        $blockOptionsHtml.='<select multiple="multiple"
                            size="5"
                            id="bundle-option-'.$_option->getId().'"
                            name="bundle_option['.$_option->getId().'][]"
                            data-selector="bundle_option['.$_option->getId().'][]"
                            class="bundle-option-'.$_option->getId().' multiselect product bundle option change-container-classname"';
        if ($_option->getRequired()) {
            $blockOptionsHtml.='data-validate={required:true}';
        }
        $blockOptionsHtml.='>';
        if (!$_option->getRequired()) {
            $blockOptionsHtml.='<option value="">'.__('None').'</option>';
        }
        foreach ($_selections as $_selection) {
            if ($_selection->getOptionId() == $_option->getId()) {
                $amount = $price->getOptionSelectionAmount($_selection);
                $blockOptionsHtml.='<option value="'.$_selection->getSelectionId().'" price="'.$amount.'"';
                if ($blockOptionbl->isSelected($_selection)) {
                    $blockOptionsHtml.=' selected="selected"';
                }
                if (!$_selection->isSaleable()) {
                    $blockOptionsHtml.=' disabled="disabled"';
                }

                $blockOptionsHtml.='>';
                $blockOptionsHtml.= $blockOptionbl->getSelectionQtyTitlePrice($_selection, false);
                $blockOptionsHtml.='</option>';
            }
        }
        $blockOptionsHtml.='</select></div></div>';
        return $blockOptionsHtml;
    }

    /**
     * @param $_option
     * @param $_selections
     * @param $blockOptionbl
     * @param $price
     * @return string
     */
    public function getBOptionRadio($_option, $_selections, $blockOptionbl, $price)
    {
        $amount = 0;
        $blockOptionsHtml = '';
        $_default = $_option->getDefaultSelection();
        $blockOptionsHtml.='<div class="field option ';
        if ($_option->getRequired()) {
            $blockOptionsHtml.= 'required';
        }
        $blockOptionsHtml.='">';
        $blockOptionsHtml.='<label class="label"><span>'.htmlspecialchars($_option->getTitle()).'</span></label>';
        $blockOptionsHtml.='<div class="control">
                <div class="nested options-list">';

        if (!$_option->getRequired()) {
            $blockOptionsHtml.='<div class="field choice">
                                    <input type="radio"
                                           class="radio product bundle option"
                                           id="bundle-option-'.$_option->getId().'"
                                           name="bundle_option['.$_option->getId().']"
                                           data-selector="bundle_option['.$_option->getId().']"';
            if (!$_default && !$_default->isSalable()) {
                $blockOptionsHtml.='checked="checked" ';
            }
            $blockOptionsHtml.='value=""/>
                                    <label class="label" for="bundle-option-'.$_option->getId().'">
                                        <span>'. __('None').'</span>
                                    </label>
                                </div>';
        }

        foreach ($_selections as $_selection) {
            if ($_selection->getOptionId() == $_option->getId()) {
                $blockOptionsHtml.='<div class="field choice">
                                    <input type="radio"
                                           class="radio product bundle option change-container-classname"
                                           id="bundle-option-'.$_option->getId().'-'.$_selection->getSelectionId().'"';
                if ($_option->getRequired()) {
                    $blockOptionsHtml.='data-validate="{\'validate-one-required-by-name\':true}"';
                }
                $blockOptionsHtml.='name="bundle_option['.$_option->getId().']"
                                           data-selector="bundle_option['.$_option->getId().']"';
                if ($blockOptionbl->isSelected($_selection)) {
                    $blockOptionsHtml.='checked="checked"';
                }
                if (!$_selection->isSaleable()) {
                    $blockOptionsHtml.=' disabled="disabled"';
                }
                $amount = $price->getOptionSelectionAmount($_selection);
                $blockOptionsHtml.='value="'.$_selection->getSelectionId().'"  price="'.$amount.'" />
                                    <label class="label"
                                           for="bundle-option-'.$_option->getId().'-'.$_selection->getSelectionId().'">
                                        <span>'.$blockOptionbl->getSelectionTitlePrice($_selection).'</span>
                                    </label>
                                </div>';
            }
        }
        $blockOptionsHtml.='<div id="bundle-option-'.$_option->getId().'-container"></div>';

        $blockOptionsHtml.='<div class="field qty qty-holder">
                        <label class="label" for="bundle-option-'.$_option->getId().'-qty-input">
                            <span>'.__('Quantity').'</span>
                        </label>';
        $blockOptionsHtml.='<div class="control">
                            <input id="bundle-option-'.$_option->getId().'-qty-input"
                                   class="input-text qty"
                                   type="number"
                                   name="bundle_option_qty['.$_option->getId().']"
                                   data-selector="bundle_option_qty['.$_option->getId().']"
                                   value="1" style="width: 3.2em;"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        return $blockOptionsHtml;
    }

    /**
     * @param $_option
     * @param $_selections
     * @param $blockOptionbl
     * @param $price
     * @return string
     */
    public function getBOptionSelect($_option, $_selections, $blockOptionbl, $price)
    {
        $amount = 0;
        $blockOptionsHtml = '';
        $blockOptionsHtml.='<div class="field option ';
        if ($_option->getRequired()) {
            $blockOptionsHtml.= 'required';
        }
        $blockOptionsHtml.='">';
        $blockOptionsHtml.='<label class="label"><span>'.htmlspecialchars($_option->getTitle()).'</span></label>';
        $blockOptionsHtml.='<div class="control">';
        
        $blockOptionsHtml.='<select id="bundle-option-'.$_option->getId().'"
                            name="bundle_option['.$_option->getId().']"
                            data-selector="bundle_option['.$_option->getId().']"
                            class="bundle-option-'.$_option->getId().' product bundle option bundle-option-select change-container-classname"';
        if ($_option->getRequired()) {
            $blockOptionsHtml.='data-validate = {required:true}';
        }
        $blockOptionsHtml.='>';
        $blockOptionsHtml.='<option value="">'.__('Choose a selection...').'</option>';
        foreach ($_selections as $_selection) {
            if ($_selection->getOptionId() == $_option->getId()) {
                $amount = $price->getOptionSelectionAmount($_selection);
                $blockOptionsHtml.='<option value="'.$_selection->getSelectionId().'" price="'.$amount.'"';
                if ($blockOptionbl->isSelected($_selection)) {
                    $blockOptionsHtml.=' selected="selected"';
                }
                if (!$_selection->isSaleable()) {
                    $blockOptionsHtml.=' disabled="disabled"';
                }

                $arr = explode('+', strip_tags($blockOptionbl->getSelectionTitlePrice($_selection, false)));
                
                if ($arr[1]) {
                    $arrr = explode(' ', $arr[1]);
                    $arrr = array_values(array_filter($arrr, function($value) { return trim($value) !== ''; }));
                    if ($arrr[1]) {
                        $label = strip_tags($blockOptionbl->getSelectionTitlePrice($_selection, false));
                        $label = str_replace($arrr[1], '(Excl. tax: '.trim($arrr[1]).')', $label);
                    }
                }
                $blockOptionsHtml.='>'.$label.'</option>';
            }
        }
        $blockOptionsHtml.='</select>';

        $blockOptionsHtml.='<div class="nested">
                    <div class="field qty qty-holder">
                        <label class="label" for="bundle-option-'.$_option->getId().'-qty-input">
                            <span>'.__('Quantity').'</span>
                        </label>
                        <div class="control">
                            <input id="bundle-option-'.$_option->getId().'-qty-input"
                                   class="input-text qty"
                                   type="number"
                                   name="bundle_option_qty['.$_option->getId().']"
                                   data-selector="bundle_option_qty['.$_option->getId().']"
                                   value="1"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        return $blockOptionsHtml;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPriceHtml(\Magento\Catalog\Model\Product $product)
    {
        $resultPage = $this->resultPageFactory->create();
        $priceRender = $resultPage->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * @param $_option
     * @param $product
     * @return string
     */
    public function getValuesHtml($_option, $product)
    {
        $configValue = $product->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $product->getStore();

        $class = ($_option->getIsRequire()) ? ' required' : '';
        $html = '';

        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD ||
            $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA
        ) {
           $html =  $this->getCustomOptionText($product, $_option, $class, $configValue, $store);
        }
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE_TIME ||
            $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE ||
            $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_TIME
        ) {
            $html =  $this->getCustomOptionTime($product, $_option, $class, $configValue, $store);
        }

        // if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_FILE) {
            
        // }

        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN ||
            $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_MULTIPLE
        ) {
            $html =  $this->getCustomOptionDropdownMuiltiple($product, $_option, $class, $configValue, $store);
        }

        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO ||
            $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX
        ) {
            $html =  $this->getCustomOptionRadioCheckbox($product, $_option, $class, $configValue, $store);
        }
        return $html;
    }

    // get custom option

    /**
     * @param $product
     * @param $_option
     * @param $class
     * @param $configValue
     * @param $store
     * @return string
     */
    public function getCustomOptionText($product, $_option, $class, $configValue, $store)
    {
        $html = '';
        $html .= '<div class="field';
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) {
            $html .= ' textarea ';
        }
        $html .= $class.'">';
        $html .='<label class="label" for="options_'.$_option->getId().'_text">
        <span>'.htmlspecialchars($_option->getTitle()).'</span>
        </label>';

        $html .='<div class="control">';
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD) {
            $html .= $this->getCustomOptionTextFiled($product, $_option, $store);
        }
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) {
            $html .= $this->getCustomOptionTextArea($product, $_option, $store);
        }
        if ($_option->getMaxCharacters()) {
            $html .='<p class="note">Maximum number of characters:
                <strong>'.$_option->getMaxCharacters().'</strong></p>';
        }
        $html .='</div></div>';
        return $html;
    }

    /**
     * @param $product
     * @param $_option
     * @param $store
     * @return string
     */
    public function getCustomOptionTextFiled($product, $_option, $store)
    {
        $html = '';
        $_textValidate = null;
        if ($_option->getIsRequire()) {
            $_textValidate['required'] = true;
        }
        if ($_option->getMaxCharacters()) {
            $_textValidate['maxlength'] = $_option->getMaxCharacters();
        }
        $html .='<input type="text"
               id="options_'.$_option->getId().'_text"
               class="input-text product-custom-option"';
        if (!empty($_textValidate)) {
            $html .='data-validate="'.htmlspecialchars(json_encode($_textValidate)).'"';
        }
        if ($this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false) > 0) {
            $html .='price="'.$this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false).'"';
        }
        $html .='name="options['.$_option->getId().']"
               data-selector="options['.$_option->getId().']"
               value="'. htmlspecialchars($product->getPreconfiguredValues()->getData('options/' . $_option->getId())).'"/>';
        return $html;
    }

    /**
     * @param $product
     * @param $_option
     * @param $store
     * @return string
     */
    public function getCustomOptionTextArea($product, $_option, $store)
    {
        $html = '';
        $_textAreaValidate = null;
        if ($_option->getIsRequire()) {
            $_textAreaValidate['required'] = true;
        }
        if ($_option->getMaxCharacters()) {
            $_textAreaValidate['maxlength'] = $_option->getMaxCharacters();
        }
        $html .='<textarea id="options_'.$_option->getId().'_text"
                  class="product-custom-option"';
        if (!empty($_textAreaValidate)) {
            $html .='data-validate="'.htmlspecialchars(json_encode($_textAreaValidate)).'"';
        }
        if ($this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false) > 0) {
            $html .='price="'.$this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false).'"';
        }
        $html .='name="options['.$_option->getId().']"
                  data-selector="options['.$_option->getId().']"
                  rows="5"
                  cols="25">'.htmlspecialchars($product->getPreconfiguredValues()->getData('options/' . $_option->getId())).'</textarea>';
        return $html;
    }

    /**
     * @param $product
     * @param $_option
     * @param $class
     * @param $configValue
     * @param $store
     * @return string
     */
    public function getCustomOptionTime($product, $_option, $class, $configValue, $store)
    {
        $html = '';
        $html .='<div class="field date'.$class.'"';
        $html .='">
            <fieldset class="fieldset fieldset-product-options-inner'.$class.'">
                <legend class="legend">
                    <span>'.htmlspecialchars($_option->getTitle()).'</span>                        
                </legend>';
        $html .='<div class="control">';
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE_TIME
            || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE) {
            $html .= $this->getDateHtml($_option, $product);
        }

        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE_TIME
            || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_TIME) {
            $html .= $this->getTimeHtml($_option, $product);
        }

        if ($_option->getIsRequire()) {
            $html .='<input type="hidden"
                               name="validate_datetime_'.$_option->getId().'"
                               class="validate-datetime-'.$_option->getId().'"
                               price="'.$this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false).'"
                               value=""
                               data-validate="{"validate-required-datetime":'.$_option->getId().'}"/>';
        } else {
            $html .='<input type="hidden"
                               name="validate_datetime_'.$_option->getId().'"
                               class="validate-datetime-'.$_option->getId().'"
                               price="'.$this->pricingHelper->currencyByStore($_option->getPrice(true), $store, false).'"
                               value=""
                               data-validate="{"validate-optional-datetime":'.$_option->getId().'}"/>';
        }
       
        $html .='</div></fieldset></div>';
        return $html;
    }

    /**
     * @param $product
     * @param $_option
     * @param $class
     * @param $configValue
     * @param $store
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomOptionDropdownMuiltiple($product, $_option, $class, $configValue, $store)
    {
        $html = '';
        $html .= '<div class="field'.$class.'">';
        $html .='<label class="label" for="select_'.$_option->getId().'">
                <span>'.htmlspecialchars($_option->getTitle()).'</span>
                </label>';
        $html .='<div class="control">';
        $extraParams = '';
        $select = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setData(
            [
                'id' => 'select_' . $_option->getId(),
                'class' => $class . ' product-custom-option admin__control-select'
            ]
        );
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN) {
            $select->setName('options[' . $_option->getid() . ']')->addOption('', __('-- Please Select --'));
        } else {
            $select->setName('options[' . $_option->getid() . '][]');
            $select->setClass('multiselect admin__control-multiselect' . $class . ' product-custom-option');
        }
        foreach ($_option->getValues() as $_value) {
            $priceStr = $this->_formatPrice(
                $_option,
                $product,
                [
                    'is_percent' => $_value->getPriceType() == 'percent',
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                ],
                false
            );

            $arr = explode(' ', strip_tags($priceStr));
            $arr = array_values(array_filter($arr, function($value) { return trim($value) !== ''; }));
            $priceText = strip_tags($priceStr);
            if (isset($arr[2])) {
                $priceText = str_replace($arr[2], '(Excl. tax: '.trim($arr[2]).')', $priceText);
            }
            $select->addOption(
                $_value->getOptionTypeId(),
                $_value->getTitle() . ' ' . $priceText . '',
                ['price' => $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false)]
            );
        }
        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_MULTIPLE) {
            $extraParams = ' multiple="multiple"';
        }
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        $select->setExtraParams($extraParams);

        if ($configValue) {
            $select->setValue($configValue);
        }
        $html .= $select->getHtml();
        $html .='</div></div>';
        return $html;
    }

    /**
     * @param $product
     * @param $_option
     * @param $class
     * @param $configValue
     * @param $store
     * @return string
     */
    public function getCustomOptionRadioCheckbox($product, $_option, $class, $configValue, $store)
    {
        $html = '';
        $html .= '<div class="field'.$class.'">';
        $html .='<label class="label" for="select_'.$_option->getId().'">
                <span>'.htmlspecialchars($_option->getTitle()).'</span>
                </label>';
        $html .='<div class="control">';
        $selectHtml = '<div class="options-list nested" id="options-' . $_option->getId() . '-list">';
        $arraySign = '';
        switch ($_option->getType()) {
            case \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO:
                $type = 'radio';
                $classs = 'radio admin__control-radio';
                if (!$_option->getIsRequire()) {
                    $selectHtml .= '<div class="field choice admin__field admin__field-option">' .
                        '<input type="radio" id="options_' .
                        $_option->getId() .
                        '" class="' .
                        $classs .
                        ' product-custom-option" name="options[' .
                        $_option->getId() .
                        ']"' .
                        ' data-selector="options[' . $_option->getId() . ']"' .
                        ' value="" checked="checked" /><label class="label admin__field-label" for="options_' .
                        $_option->getId() .
                        '"><span>' .
                        __('None') . '</span></label></div>';
                }
                break;
            case \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX:
                $type = 'checkbox';
                $classs = 'checkbox admin__control-checkbox';
                $arraySign = '[]';
                break;
        }
        $count = 1;
        foreach ($_option->getValues() as $_value) {
            $count++;

            $priceStr = $this->_formatPrice(
                $_option,
                $product,
                [
                    'is_percent' => $_value->getPriceType() == 'percent',
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                ]
            );

            $htmlValue = $_value->getOptionTypeId();
            if ($arraySign) {
                $checked = is_array($configValue) && in_array($htmlValue, $configValue) ? 'checked' : '';
            } else {
                $checked = $configValue == $htmlValue ? 'checked' : '';
            }

            $dataSelector = 'options[' . $_option->getId() . ']';
            if ($arraySign) {
                $dataSelector .= '[' . $htmlValue . ']';
            }

            $selectHtml .= '<div class="field choice admin__field admin__field-option' .
                $class .
                '">' .
                '<input type="' .
                $type .
                '" class="' .
                $classs .
                ' ' .
                $class .
                ' product-custom-option"' .
                ' name="options[' .
                $_option->getId() .
                ']' .
                $arraySign .
                '" id="options_' .
                $_option->getId() .
                '_' .
                $count .
                '" value="' .
                $htmlValue .
                '" ' .
                $checked .
                ' data-selector="' . $dataSelector . '"' .
                ' price="' .
                $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                '" />' .
                '<label class="label admin__field-label" for="options_' .
                $_option->getId() .
                '_' .
                $count .
                '"><span>' .
                $_value->getTitle() .
                '</span> ' .
                $priceStr .
                '</label>';
            $selectHtml .= '</div>';
        }
        $selectHtml .= '</div>';
        $html .= $selectHtml;
        if ($_option->getIsRequire()) {
            $html .='<span id="options-'.$_option->getId() .'-container"></span>';
        }
        $html .='</div></div>';

        return $html;
    }

    /**
     * @param $option
     * @param $product
     * @param $value
     * @param bool $flag
     * @return string
     */
    protected function _formatPrice($option, $product, $value, $flag = true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;
        $resultPage = $this->resultPageFactory->create();
        $customOptionPrice = $product->getPriceInfo()->getPrice('custom_option_price');
        $context = [CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG => true];
        $optionAmount = $customOptionPrice->getCustomAmount($value['pricing_value'], null, $context);
        $priceStr .= $resultPage->getLayout()->getBlock('product.price.render.default')->renderAmount(
            $optionAmount,
            $customOptionPrice,
            $product
        );
        if ($flag) {
            $priceStr = '<span class="price-notice">' . $priceStr . '</span>';
        }

        return $priceStr;
    }

    /**
     * @param $_option
     * @param $product
     * @return string
     */
    public function getDateHtml($_option, $product)
    {
        if ($this->catalogProductOptionTypeDate->useCalendar()) {
            return $this->getCalendarDateHtml($_option, $product);
        } else {
            return $this->getDropDownsDateHtml($_option, $product);
        }
    }

    /**
     * @param $_option
     * @param $product
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCalendarDateHtml($_option, $product)
    {
        $value = $product->getPreconfiguredValues()->getData('options/' . $_option->getId() . '/date');

        $yearStart = $this->catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->catalogProductOptionTypeDate->getYearEnd();

        $calendar = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Date::class
        )->setId(
            'options_' . $_option->getId() . '_date'
        )->setName(
            'options[' . $_option->getId() . '][date]'
        )->setClass(
            'product-custom-option datetime-picker input-text'
        )->setImage(
            $this->getViewFileUrl('Magento_Theme::calendar.png')
        )->setDateFormat(
            $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT)
        )->setValue(
            $value
        )->setYearsRange(
            $yearStart . ':' . $yearEnd
        );

        return $calendar->getHtml();
    }

    /**
     * @param $_option
     * @param $product
     * @return string
     */
    public function getDropDownsDateHtml($_option, $product)
    {
        $fieldsSeparator = '&nbsp;';
        $fieldsOrder = $this->catalogProductOptionTypeDate->getConfigData('date_fields_order');
        $fieldsOrder = str_replace(',', $fieldsSeparator, $fieldsOrder);

        $monthsHtml = $this->_getSelectFromToHtml($_option, $product, 'month', 1, 12);
        $daysHtml = $this->_getSelectFromToHtml($_option, $product, 'day', 1, 31);

        $yearStart = $this->catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->catalogProductOptionTypeDate->getYearEnd();
        $yearsHtml = $this->_getSelectFromToHtml($_option, $product, 'year', $yearStart, $yearEnd);

        $translations = ['d' => $daysHtml, 'm' => $monthsHtml, 'y' => $yearsHtml];
        return strtr($fieldsOrder, $translations);
    }

    /**
     * @param $_option
     * @param $product
     * @return string
     */
    public function getTimeHtml($_option, $product)
    {
        if ($this->catalogProductOptionTypeDate->is24hTimeFormat()) {
            $hourStart = 0;
            $hourEnd = 23;
            $dayPartHtml = '';
        } else {
            $hourStart = 1;
            $hourEnd = 12;
            $dayPartHtml = $this->_getHtmlSelect(
                $_option,
                $product,
                'day_part'
            )->setOptions(
                ['am' => __('AM'), 'pm' => __('PM')]
            )->getHtml();
        }
        $hoursHtml = $this->_getSelectFromToHtml($_option, $product, 'hour', $hourStart, $hourEnd);
        $minutesHtml = $this->_getSelectFromToHtml($_option, $product, 'minute', 0, 59);

        return $hoursHtml . '&nbsp;<b>:</b>&nbsp;' . $minutesHtml . '&nbsp;' . $dayPartHtml;
    }

    /**
     * @param $_option
     * @param $product
     * @param $name
     * @param $from
     * @param $to
     * @param null $value
     * @return mixed
     */
    protected function _getSelectFromToHtml($_option, $product, $name, $from, $to, $value = null)
    {
        $options = [['value' => '', 'label' => '-']];
        for ($i = $from; $i <= $to; $i++) {
            $options[] = ['value' => $i, 'label' => $this->_getValueWithLeadingZeros($i)];
        }
        return $this->_getHtmlSelect($_option, $product, $name, $value)->setOptions($options)->getHtml();
    }

    /**
     * @param $_option
     * @param $product
     * @param $name
     * @param null $value
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getHtmlSelect($_option, $product, $name, $value = null)
    {
        $require = '';
        $select = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setId(
            'options_' . $_option->getId() . '_' . $name
        )->setClass(
            'product-custom-option admin__control-select datetime-picker' . $require
        )->setExtraParams()->setName(
            'options[' . $_option->getId() . '][' . $name . ']'
        );

        $extraParams = 'style="width:auto"';

        $extraParams .= ' data-role="calendar-dropdown" data-calendar-role="' . $name . '"';
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        if ($_option->getIsRequire()) {
            $extraParams .= ' data-validate=\'{"datetime-validation": true}\'';
        }

        $select->setExtraParams($extraParams);
        if ($value === null) {
            $value = $product->getPreconfiguredValues()->getData(
                'options/' . $_option->getId() . '/' . $name
            );
        }
        if ($value !== null) {
            $select->setValue($value);
        }

        return $select;
    }

    /**
     * @param $value
     * @return string
     */
    protected function _getValueWithLeadingZeros($value)
    {
        return $value < 10 ? '0' . $value : $value;
    }

    /**
     * @param $product
     * @return float|int
     */
    public function taxRate($product)
    {
        $store = $this->_storeManager->getStore();
        $taxCalculation = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Tax\Model\Calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        $percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));
        return ($percent/100);
    }

    /**
     * @return string
     */
    public function geturlAddMultipleToCart()
    {
        return $this->getUrl('addmuntiple/cart/addMuntiple');
    }

    /**
     * @param $id
     * @return $this
     */
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }

    /**
     * @return bool
     */
    public function isShowProductImage()
    {
        return $this->helperData->isShowProductImage();
    }

    /**
     * @return bool
     */
    public function isShowProductPrice()
    {
        return $this->helperData->isShowProductPrice();
    }

    /**
     * @return bool
     */
    public function isShowCartInfo()
    {
        return $this->helperData->isShowCartInfo();
    }

    /**
     * @return bool
     */
    public function isShowCheckoutLink()
    {
        return $this->helperData->isShowCheckoutLink();
    }

    /**
     * @return mixed|string
     */
    public function getTextbuttonaddmt()
    {
        return $this->helperData->getTextbuttonaddmt();
    }

    /**
     * @return bool
     */
    public function isShowContinueBtn()
    {
        return $this->helperData->isShowContinueBtn();
    }

    /**
     * @return mixed
     */
    public function getCountDownActive()
    {
        return $this->helperData->getCountDownActive();
    }

    /**
     * @return mixed
     */
    public function getCountDownTime()
    {
        return $this->helperData->getCountDownTime();
    }

    /**
     * @return mixed
     */
    public function getItemonslide()
    {
        return $this->helperData->getItemonslide();
    }

    /**
     * @return mixed
     */
    public function getSlidespeed()
    {
        return $this->helperData->getSlidespeed();
    }

    /**
     * @return mixed
     */
    public function getSlideauto()
    {
        return $this->helperData->getSlideauto();
    }

    /**
     * @return mixed|string
     */
    public function getBtnContinueBackground()
    {
        return $this->helperData->getBtnContinueBackground();
    }

    /**
     * @return mixed|string
     */
    public function getBtnContinueHover()
    {
        return $this->helperData->getBtnContinueHover();
    }

    /**
     * @return string
     */
    public function getBtnContinueText()
    {
        return $this->helperData->getBtnContinueText();
    }

    /**
     * @return mixed|string
     */
    public function getBtnViewcartBackground()
    {
        return $this->helperData->getBtnViewcartBackground();
    }

    /**
     * @return mixed|string
     */
    public function getBtnViewcartHover()
    {
        return $this->helperData->getBtnViewcartHover();
    }

    /**
     * @return string
     */
    public function getBtnViewcartText()
    {
        return $this->helperData->getBtnViewcartText();
    }

    /**
     * @return int
     */
    public function getImageSizemt()
    {
        return $this->helperData->getImageSizemt();
    }

    /**
     * @return int
     */
    public function getImageSizesg()
    {
        return $this->helperData->getImageSizesg();
    }

    /**
     * @return int
     */
    public function getImageSizeer()
    {
        return $this->helperData->getImageSizeer();
    }

    /**
     * @param $price
     * @return float|string
     */
    public function fomatPricePopup($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * @see \Magento\Catalog\Model\Product\Image
     * @param int $size
     * @return $this
     */
    public function resizeImage($product, $imageId, $size)
    {
        $resizedImage = $this->productImageHelper
                           ->init($product, $imageId)
                           ->constrainOnly(true)
                           ->keepAspectRatio(true)
                           ->keepTransparency(true)
                           ->keepFrame(false)
                           ->resize($size, $size);
        return $resizedImage;
    }
}
