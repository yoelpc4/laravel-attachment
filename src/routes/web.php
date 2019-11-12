<?php

Route::group(
    [
        'namespace' => 'Yoelpc4\LaravelAttachment\Http\Controllers',
        'domain'    => config('attachment.routes.web.domain'),
        'prefix'    => config('attachment.routes.web.prefix'),
        'as'        => config('attachment.routes.web.name-prefix'),
    ],
    function () {
        Route::resource('attachments', 'AttachmentController')
            ->only(['store', 'find', 'destroy'])
            ->middleware(config('attachment.routes.web.middleware'));
    }
);
