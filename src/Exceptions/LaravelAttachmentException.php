<?php

namespace Yoelpc4\LaravelAttachment\Exceptions;

use Exception;
use Yoelpc4\LaravelAttachment\Contracts\FileAttachment;

class LaravelAttachmentException extends Exception
{
    /**
     * Display message for unmapped exception
     *
     * @param  string  $key
     * @return LaravelAttachmentException
     */
    public static function unmapped(string $key)
    {
        return new static("The {$key} is not registered in file attachment map");
    }

    /**
     * Display message for invalid instance exception
     *
     * @param  object  $object
     * @return LaravelAttachmentException
     */
    public static function invalidInstance($object)
    {
        $className = get_class($object);

        return new static("The {$className} must be an instance of ".FileAttachment::class);
    }
}
