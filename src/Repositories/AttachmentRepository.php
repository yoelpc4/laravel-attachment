<?php

namespace Yoelpc4\LaravelAttachment\Repositories;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Yoelpc4\LaravelAttachment\Contracts\AttachmentRepository as AttachmentRepositoryContract;
use Yoelpc4\LaravelAttachment\Models\Attachment;

class AttachmentRepository implements AttachmentRepositoryContract
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  array  $data
     * @return Attachment
     * @throws Throwable
     */
    public function create(array $data)
    {
        try {
            return \DB::transaction(function () use ($data) {
                return Attachment::create($data);
            });
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Attachment
     * @throw ModelNotFoundException
     */
    public function find(int $id)
    {
        try {
            return Attachment::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool|null
     * @throws ModelNotFoundException
     * @throws Exception
     * @throws Throwable
     */
    public function delete(int $id)
    {
        try {
            return \DB::transaction(function () use ($id) {
                $attachment = $this->find($id);

                return $attachment->delete();
            });
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
