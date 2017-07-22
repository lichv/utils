<?php

namespace Utils\Facades;

use Illuminate\Support\Facades\Facade;
use Utils\Contracts\Factory;

/**
 * @see \Utils\SocialiteManager
 */
class Sms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
