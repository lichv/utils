<?php

namespace Utils\resources;

use Utils\resources\interfaces\ResourceInterface;

/**
 * Class GetFileUrl
 * @package Mixaroot\Downloadimages\resources
 * @inheritdoc
 */
class GetFileUrl implements ResourceInterface
{
    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param $path
     * @return resource
     * @inheritdoc
     */
    public function get()
    {
        return fopen($this->path, "rb");
    }
}