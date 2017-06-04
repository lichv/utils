<?php

namespace Utils\resources\interfaces;

/**
 * Resource for work with file
 * Get resource only by url, without more parameters
 * Interface ResourceInterface
 * @package Mixaroot\Downloadimages\resources\interfaces
 */
interface ResourceInterface
{
    /**
     * Get resource
     * @return mixed
     */
    public function get();
}