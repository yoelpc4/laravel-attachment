<?php

namespace Yoelpc4\LaravelAttachment\Utils;

trait AttachmentFilesystem
{
    /**
     * Get filesystem disk
     *
     * @return string
     */
    public function getDisk()
    {
        return 'public';
    }

    /**
     * Get filesystem directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return 'attachments';
    }
}
