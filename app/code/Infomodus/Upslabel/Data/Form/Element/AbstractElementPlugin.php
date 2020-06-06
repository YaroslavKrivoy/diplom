<?php
namespace Infomodus\Upslabel\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
class AbstractElementPlugin {
    /**
     * @link https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
     * @see \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
     * @param AbstractElement $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetHtmlAttributes(AbstractElement $subject, $result) {
        $result[]= 'autocomplete';
        return $result;
    }

    /**
     * @link https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
     * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
     * @param AbstractElement $subject
     * @return array()
     */
    public function beforeGetElementHtml(AbstractElement $subject) {
        $subject['autocomplete'] = 'new-password';
        return [];
    }
}
