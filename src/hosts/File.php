<?php

namespace Utils\hosts;

use Utils\hosts\interfaces\FileInterfaces;
use Utils\hosts\interfaces\ResourceInterfaces;
use Utils\resources\SetFileUrl;

/**
 * Get resource for write
 * Class File
 * @package Mixaroot\Downloadimages\hosts
 */
class File implements FileInterfaces, ResourceInterfaces
{
    /**
     * Path for write
     * @var string
     */
    private $path = '';
    /**
     * File name for write
     * @var string
     */
    private $name = '';

    /**
     * Set path for save files
     * @param string $path
     * @throws \Exception
     */
    public function setPath(string $path)
    {
        if (!is_dir($path)) {
            throw new \Exception('Wrong path. It is not directory');
        }
        $this->path = rtrim($path, '\\/') . DIRECTORY_SEPARATOR;
    }

    /**
     * Set name for save file
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get resource for save
     * @return mixed
     * @throws \Exception
     */
    public function getResource()
    {
        $pathToFile = $this->path . $this->name;
        if (file_exists($pathToFile)) {
            throw new \Exception('file exist in path ' . $pathToFile);
        }
        return new SetFileUrl($pathToFile);
    }
}