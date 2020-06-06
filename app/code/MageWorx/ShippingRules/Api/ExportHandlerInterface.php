<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

interface ExportHandlerInterface
{
    /**
     * Get content as a CSV string
     *
     * @param array $entities
     * @param array $ids
     * @return string
     */
    public function getContent($entities = [], $ids = []);
}