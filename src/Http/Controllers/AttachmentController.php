<?php

namespace Yoelpc4\LaravelAttachment\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Throwable;
use Yoelpc4\LaravelAttachment\Contracts\AttachmentRepository;
use Yoelpc4\LaravelAttachment\Exceptions\LaravelAttachmentException;

class AttachmentController extends Controller
{
    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * AttachmentController constructor.
     *
     * @param  AttachmentRepository  $attachmentRepository
     */
    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if ($request->has('file_attachment')) {
            try {
                $fileAttachment = \AttachmentManager::getFileAttachment($request->file_attachment);
            } catch (LaravelAttachmentException $e) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $request->validate([
                'attachable_type' => 'required|string',
                'attachable_id'   => 'nullable|numeric',
                'attachment'      => $fileAttachment->getValidationRules()
            ]);

            $file = $request->file('attachment');

            if ($path = $file->store($fileAttachment->getDirectory(), $fileAttachment->getDisk())) {
                $data = [
                    'attachable_type' => $request->attachable_type,
                    'file_attachment' => $request->file_attachment,
                    'name'            => $file->getClientOriginalName(),
                    'path'            => $path,
                    'size'            => $file->getSize(),
                    'type'            => $file->getClientMimeType()
                ];

                if (isset($request->attachable_id)) {
                    $data['attachable_id'] = $request->attachable_id;
                }

                try {
                    $attachment = $this->attachmentRepository->create($data);
                } catch (Throwable $e) {
                    return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return response()->json(['data' => $attachment]);
            }

            return response()->json(['message' => \Lang::get('laravel-attachment::failed.delete')],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $translatedFileAttachment = \Lang::get('file_attachment');

        $data = [
            'message' => 'The given data was invalid.',
            'errors'  => [
                'file_attachment' => [
                    \Lang::get('validation.required', [
                        'attribute' => $translatedFileAttachment
                    ]),
                    \Lang::get('validation.string', [
                        'attribute' => $translatedFileAttachment
                    ])
                ]
            ]
        ];

        return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function find(int $id)
    {
        try {
            $attachment = $this->attachmentRepository->find($id);

            return response()->json(['data' => $attachment]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete the specified attachment from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $attachment = $this->attachmentRepository->find($id);

            try {
                if ($this->attachmentRepository->delete($attachment->id)) {
                    try {
                        $fileAttachment = \AttachmentManager::getFileAttachment($attachment->file_attachment);
                    } catch (LaravelAttachmentException $e) {
                        return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    if (\Storage::disk($fileAttachment->getDisk())->delete($attachment->path)) {
                        return response()->json([], Response::HTTP_NO_CONTENT);
                    }

                    return response()->json(['message' => \Lang::get('laravel-attachment::failed.delete')],
                        Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            } catch (Throwable $e) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
