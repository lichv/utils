<?php

namespace Utils\hosts\interfaces;

/**
 * Preparing path for save
 * Interface FileInterfaces
 * @package Mixaroot\Downloadimages\interfaces
 */
interface FileInterfaces
{
    /**
     * Set folder for save image
     * @param string $path
     * @return mixed
     */
    public function setPath(string $path);

    /**
     * Set name for save images
     * @param string $name
     * @return mixed
     */
    public function setName(string $name);
}