<?php

namespace Yoelpc4\LaravelAttachment;

use Yoelpc4\LaravelAttachment\Contracts\FileAttachment;
use Yoelpc4\LaravelAttachment\Exceptions\LaravelAttachmentException;
use Yoelpc4\LaravelAttachment\Utils\ClassNameFinder;

class AttachmentManager
{
    /**
     * @var array
     */
    protected $fileAttachmentMap = [];

    /**
     * Get registered file attachment map
     *
     * @return array
     */
    public function getFileAttachmentMap()
    {
        return $this->fileAttachmentMap;
    }

    /**
     * Get file attachment object
     *
     * @param  string  $key
     * @return \Yoelpc4\LaravelAttachment\Contracts\FileAttachment
     * @throws \Yoelpc4\LaravelAttachment\Exceptions\LaravelAttachmentException
     */
    public function getFileAttachment(string $key)
    {
        if (! array_key_exists($key, $this->fileAttachmentMap)) {
            throw LaravelAttachmentException::unmapped($key);
        }

        $fileAttachmentClassName = $this->fileAttachmentMap[$key];

        $fileAttachment = new $fileAttachmentClassName;

        if (! $fileAttachment instanceof FileAttachment) {
            throw LaravelAttachmentException::invalidInstance($fileAttachment);
        }

        return $fileAttachment;
    }

    /**
     * Load file attachment map.
     *
     * @return void
     */
    public function loadFileAttachmentMap()
    {
        $classNameFinder = new ClassNameFinder;

        $fileAttachmentsNamespace = app()->getNamespace().'FileAttachments';

        $classNames = $classNameFinder->getClassNamesByNamespace($fileAttachmentsNamespace);

        $fileAttachments = collect($classNames)
            ->mapWithKeys(function ($className) {
                $fileAttachment = new $className;

                if ($fileAttachment instanceof FileAttachment) {
                    return [
                        $fileAttachment::getName() => $className
                    ];
                }

                throw LaravelAttachmentException::invalidInstance($fileAttachment);
            })
            ->toArray();

        $this->fileAttachmentMap = $fileAttachments;
    }
}
