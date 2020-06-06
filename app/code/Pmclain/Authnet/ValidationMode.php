<?php

namespace Pmclain\Authnet;

use Pmclain\Authnet\Exception\InputException;

class ValidationMode
{
    const TEST = 'testMode';
    const LIVE = 'liveMode';

    /**
     * @var string
     */
    private $mode;

    /**
     * @param string $mode
     * @return $this
     * @throws InputException
     */
    public function set($mode)
    {
        if (!in_array($mode, [self::TEST, self::LIVE])) {
            throw new InputException(
                sprintf('Invalid mode. Available modes are: %s or %s', self::TEST, self::LIVE)
            );
        }

        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string|false
     */
    public function get()
    {
        if (is_null($this->mode)) {
            return false;
        }

        return $this->mode;
    }
}
