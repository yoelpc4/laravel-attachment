<?php

Route::group(
    [
        'namespace' => 'Yoelpc4\LaravelAttachment\Http\Controllers',
        'domain'    => config('attachment.routes.api.domain'),
        'prefix'    => config('attachment.routes.api.prefix'),
        'as'        => config('attachment.routes.api.name-prefix'),
    ],
    function () {
        Route::resource('attachments', 'AttachmentController')
            ->only(['store', 'find', 'destroy'])
            ->middleware(config('attachment.routes.api.middleware'));
    }
);
