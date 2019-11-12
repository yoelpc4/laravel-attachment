<?php

namespace Yoelpc4\LaravelAttachment\Contracts;

interface FileAttachment
{
    /**
     * Get name
     *
     * @return string
     */
    public static function getName();

    /**
     * Get disk
     *
     * @return string
     */
    public function getDisk();

    /**
     * Get directory
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Get validation rules
     *
     * @return string|array
     */
    public function getValidationRules();
}
