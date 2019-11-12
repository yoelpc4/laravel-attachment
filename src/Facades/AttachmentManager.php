<?php

namespace Yoelpc4\LaravelAttachment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getFileAttachmentMap()
 * @method static \Yoelpc4\LaravelAttachment\Contracts\FileAttachment getFileAttachment(string $key)
 * @method static void loadFileAttachmentMap()
 *
 * @see \Yoelpc4\LaravelAttachment\AttachmentManager
 */
class AttachmentManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel_attachment.attachment_manager';
    }
}
