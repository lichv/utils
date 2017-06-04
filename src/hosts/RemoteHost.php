<?php

namespace Utils\hosts;

use Utils\hosts\interfaces\RemoteHostInterfaces;
use Utils\hosts\interfaces\ResourceInterfaces;
use Utils\resources\GetFileUrl;

/**
 * Class for work with remote host
 * Class RemoteHost
 * @package Mixaroot\Downloadimages\hosts
 */
class RemoteHost implements RemoteHostInterfaces, ResourceInterfaces
{
    /**
     * @var string
     */
    private $url = '';
    /**
     * @var string
     */
    private $scheme = '';
    /**
     * @var string
     */
    private $host = '';
    /**
     * @var bool
     */
    private $port = false;
    /**
     * @var string
     */
    private $user = '';
    /**
     * @var string
     */
    private $pass = '';
    /**
     * @var string
     */
    private $path = '';
    /**
     * @var array
     */
    private $query = [];
    /**
     * @var string
     */
    private $fragment = '';

    /**
     * Parse remote url
     * @param string $url
     * @throws \Exception
     */
    public function parseUrl(string $url)
    {
        $this->url = $url;
        $urlParts = parse_url($url);
        if ($urlParts === false) {
            throw new \Exception('Incorrect url');
        }
        $this->setParametersFromUrl($urlParts);
    }

    /**
     * Get remote resource
     * @return resource
     * @throws \Exception
     */
    public function getResource()
    {
        switch ($this->scheme) {
            case 'ftp':
            case 'sftp':
                throw new \Exception('Have not implemented yet');
                break;
            case 'http':
            case 'https':
            default:
                return (new GetFileUrl($this->url));
                break;
        }
    }

    /**
     * Set properties from url parameters
     * @param array $urlParts
     */
    private function setParametersFromUrl(array $urlParts)
    {
        foreach ($urlParts as $partName => $partValue) {
            if (property_exists($this, $partName)) {
                $this->{$partName} = $partValue;
            }
        }
    }
}