<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Serialize;

/**
 * Interface SerializeInterface
 * @package Aheadworks\Autorelated\Model\Serialize
 */
interface SerializeInterface
{
    /**
     * Serialize data into string
     *
     * @param mixed $data
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function serialize($data);

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function unserialize($string);
}
