<?php

namespace Utils\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $driver
     * @return \Utils\Contracts\Provider
     */
    public function driver($driver = null);
}
