<?php

namespace Utils\hosts\interfaces;

/**
 * Preparing url for get content
 * Parse url
 * Interface RemoteHostInterfaces
 * @package Mixaroot\Downloadimages\interfaces
 */
interface RemoteHostInterfaces
{
    /**
     * Parse url for set protocol, domain, port, etc
     * @param string $url
     * @return mixed
     */
    public function parseUrl(string $url);

}