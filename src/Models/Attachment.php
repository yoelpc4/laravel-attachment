<?php

namespace Yoelpc4\LaravelAttachment\Models;

use Yoelpc4\LaravelAttachment\Exceptions\LaravelAttachmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_attachment',
        'name',
        'path',
        'size',
        'type'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url'
    ];

    /**
     * The attachable polymorphic relations
     *
     * @return MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get attachment url
     *
     * @return string
     * @throws LaravelAttachmentException
     */
    public function getUrlAttribute()
    {
        try {
            $fileAttachment = \AttachmentManager::getFileAttachment($this->file_attachment);
        } catch (LaravelAttachmentException $e) {
            throw $e;
        }

        return \Storage::disk($fileAttachment->getDisk())->url($this->path);
    }
}
