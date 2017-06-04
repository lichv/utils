<?php

namespace Utils;

use Utils\hosts\RemoteHost;
use Utils\hosts\File;
use Utils\copy\Copy;

/**
 * Download image from remote host
 * Class DownloadImages
 * @package Mixaroot\Downloadimages
 */
class DownloadImages
{
    /**
     * Object for get resource. Resource for read
     * @var RemoteHost|null
     */
    private $oRemoteHost = null;
    /**
     * Object for get resource. Resource for write
     * @var File|null
     */
    private $oFile = null;
    /**
     * Object for copy first resource to second
     * @var Copy|null
     */
    private $oCopy = null;

    /**
     * Init objects for work with resources
     */
    public function __construct()
    {
        $this->oRemoteHost = new RemoteHost();
        $this->oFile = new File();
        $this->oCopy = new Copy();
    }

    /**
     * Set remote url
     * @param string $url
     * @return $this
     * @throws \Exception
     */
    public function setRemoteUrl(string $url)
    {
        $this->oRemoteHost->parseUrl($url);
        return $this;
    }

    /**
     * Set local path
     * @param string $path
     * @return $this
     */
    public function setLocalPath(string $path)
    {
        $this->oFile->setPath($path);
        return $this;
    }

    /**
     * Set local name for save
     * @param string $name
     * @return $this
     */
    public function setLocalName(string $name)
    {
        $this->oFile->setName($name);
        return $this;
    }

    /**
     * Download file from first resource to second
     * @throws \Exception
     * @return mixed
     */
    public function download()
    {
        return $this->oCopy->copyResources($this->oRemoteHost->getResource(), $this->oFile->getResource());
    }
}