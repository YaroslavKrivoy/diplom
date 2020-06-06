<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Address\Attribute\Code;

/**
 * Class Resolver
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Attribute\Code
 */
class Resolver
{
    /**
     * @var array
     */
    private $fieldsDuplicationMap = [];

    /**
     * @param array $fieldsDuplicationMap
     */
    public function __construct(
        array $fieldsDuplicationMap = []
    ) {
        $this->fieldsDuplicationMap = $fieldsDuplicationMap;
    }

    /**
     * Get duplicated attribute code
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function getDuplicatedAttributeCode($attributeCode)
    {
        if (isset($this->fieldsDuplicationMap[$attributeCode])) {
            return $this->fieldsDuplicationMap[$attributeCode];
        }
        $flippedMap = array_flip($this->fieldsDuplicationMap);
        if (isset($flippedMap[$attributeCode])) {
            return $flippedMap[$attributeCode];
        }
        return null;
    }
}
