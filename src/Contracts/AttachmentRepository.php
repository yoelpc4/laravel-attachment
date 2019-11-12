<?php

namespace Yoelpc4\LaravelAttachment\Contracts;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Yoelpc4\LaravelAttachment\Models\Attachment;

interface AttachmentRepository
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  array  $data
     * @return Attachment
     * @throws Throwable
     */
    public function create(array $data);

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Attachment
     * @throws ModelNotFoundException
     */
    public function find(int $id);

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool|null
     * @throws ModelNotFoundException
     * @throws Exception
     * @throws Throwable
     */
    public function delete(int $id);
}
