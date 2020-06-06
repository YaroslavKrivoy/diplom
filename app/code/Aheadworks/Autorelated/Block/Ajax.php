<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block;

/**
 * Class Ajax
 * @package Aheadworks\Autorelated\Block
 */
class Ajax extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $urlParams = $this->getUrlParams();
        $params = [
            'url' => $this->getUrl(
                'autorelated/view/process/',
                $urlParams
            ),
        ];

        return json_encode($params);
    }

    /**
     * Retrieve parameters for script options url
     *
     * @return array
     */
    private function getUrlParams()
    {
        $urlParams = [
            '_current' => true,
            '_secure' => $this->templateContext->getRequest()->isSecure()
        ];
        return $urlParams;
    }
}
