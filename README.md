# Laravel Attachment

[![Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Contributor Covenant][ico-code-of-conduct]](CODE_OF_CONDUCT.md)

_Laravel attachment manager with dropzone._

## Requirements

- [Laravel](https://laravel.com)
- [NPM](https://www.npmjs.com)

## Install

Require this package with composer using the following command:

```bash
composer require yoelpc4/laravel-attachment
```

## Package Resources

This package shipped with resources such as configuration, migration, routes, lang, and views.

You can also publish package configuration via command:

```bash
php artisan vendor:publish --provider="Yoelpc4\LaravelAttachment\Providers\LaravelAttachmentServiceProvider" --tag=config
```

Don't forget to running migration before deep dive into `Attachment` model part via command:

```bash
php artisan migrate
```

You can check pre-defined routes with namespace `Yoelpc4\LaravelAttachment\Http\Controllers` via command:

```bash
php artisan route:list --path=attachment
```

You can also publish package views via command:

```bash
php artisan vendor:publish --provider="Yoelpc4\LaravelAttachment\Providers\LaravelAttachmentServiceProvider" --tag=views
```

You can also publish package translations via command:

```bash
php artisan vendor:publish --provider="Yoelpc4\LaravelAttachment\Providers\LaravelAttachmentServiceProvider" --tag=lang
```

## File Attachment

The goal of this package is to build a single responsibility file attachment class, so that you can decouple 
your file attachment specific requirement from your model, controller, and request. 

Create a file attachment class via command:

```bash
php artisan make:file-attachment UserProfileImageAttachment
``` 

The default location of all file attachment classes are in `FileAttachments` directory in root namespace directory.

Adjust the name for file attachment in `getName()` method.

The default disk is `public`, you can edit the file attachment's disk by overwriting `getDisk()` method.

The default directory is `attachments`, you can edit the file attachment's directory by overwriting `getDirectory()` method. 

Adjust the validation rules for attachment request validation in `getValidationRules()` method.

## Morph Map

It is recommended to define a morph map for every `attachable` class to instruct Eloquent to use a custom name for every 
model instead of the class name. So we can decouple your database and your application's internal structure, reference: 
> https://laravel.com/docs/6.x/eloquent-relationships#custom-polymorphic-types
 
You may register the `Illuminate\Database\Eloquent\Relations\Relation::morphMap` in the boot function of your 
`AppServiceProvider` at `boot` method or create a separate service provider if you wish.

```php
public function boot()
{
    \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
        'user' => \App\User::class
    ]);
}
```

## Attachment Model

This package has `Attachment` model class to interact with attachment data in database.

The `Attachment` model has polymorphic `attachable` relation, you can use in your own model, i.e:

```php
/**
 * User has one profile image
 * 
 * @return \Illuminate\Database\Eloquent\Relations\MorphOne
 */
public function profileImage()
{
    return $this->morphOne(\Yoelpc4\LaravelAttachment\Models\Attachment::class, 'attachable')
        ->where('file_attachment', \App\FileAttachments\UserProfileImageAttachment::getName());
}

/**
 * User has many profile documents
 * 
 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
 */
public function profileDocuments()
{
    return $this->morphMany(\Yoelpc4\LaravelAttachment\Models\Attachment::class, 'attachable')
        ->where('file_attachment', \App\FileAttachments\UserProfileDocumentsAttachment::getName());
}
```

The `where` method chain must be set to scope query to include only specific file attachment criteria.

Attachment model also has `url` attribute for accessing file attachment in storage via HTTP protocol.

## Dropzone

This package has view component for reusable file uploader using [dropzone](https://www.dropzonejs.com).

The component using styles as follows:
- bootstrap
- font awesome

The component using scripts as follows:
- jquery
- bootstrap
- axios
- dropzone

The component needs a public storage symlink to display some icon, create it via command

```bash
php artisan storage:link
``` 

Please register styles & scripts in package.json, run `npm install`, adjust scripts & styles according to your 
preferences, and run `npm run dev`.

The component must have properties as follows:
- name - string
- label - string
- hint - string
- removeable - bool
- extensions - string (the list of extensions with comma as delimiter)
- maxFilesize - int (default 2mb)
- maxFiles - int (default 1)
- attachments - array (the array of model's attachment instances if it single attachment must be wrapped inside array, 
can be an empty array when creating but must be filled when editing)
- attachable_type - string (the model's custom name)
- attachable_id - string (the model's id, can empty when creating but must be an numeric when editing)
- file_attachment - string (the file attachment's name)

The code block below is an example of dropzone component setup in views:

```blade
<div class="row">
    <div class="col-md-6">
        @component('laravel-attachment::components.dropzone', [
            'name' => 'user_profile_image',
            'label' => 'Profile Image',
            'hint' => 'Allowed extensions (.jpeg,.jpg,.png) | Accept 1 file | Max. file size 10MB |
            Min. image width 200px | Min. image height 200px',
            'removeable' => true,
            'extensions' => '.jpeg,.jpg,.png',
            'maxFileSize' => 10,
            'attachments' => isset($user) ? ($user->userProfileImage()->exists() ? [$user->userProfileImage] : []) : [],
            'attachable_type' => 'user',
            'attachable_id' => isset($user) ? $user->id : '',
            'file_attachment' => 'user_profile_image'
        ])
        @endcomponent
    </div>

    <div class="col-md-6">
        @component('laravel-attachment::components.dropzone', [
            'name' => 'user_profile_document',
            'label' => 'Profile Documents',
            'hint' => 'Allowed extensions (pdf) | Accept 10 files | Max. file size 2MB',
            'removeable' => true,
            'extensions' => '.pdf',
            'maxFiles' => 10,
            'attachments' => isset($user) ? ($user->userProfileDocuments()->exists() ? $user->userProfileDocuments : []) : [],
            'attachable_type' => 'user',
            'attachable_id' => isset($user) ? $user->id : '',
            'file_attachment' => 'user_profile_documents'
        ])
        @endcomponent
    </div>
</div>
```

Add `stack` directive in your master view after your main application js for loading the component scripts after app.js.

```blade
<script src="{{ asset('js/app.js') }}"></script>
@stack('component_scripts')
```

> In your model's controller you must update the newly created attachment's `attachable_id` at `store` method i.e:

```php
if ($request->has('user_profile_image_ids')) {
    foreach ($request->user_profile_image_ids as $userProfileImageId) {
        \Yoelpc4\LaravelAttachment\Models\Attachment::find($userProfileImageId)->update([
            'attachable_id' => $user->id
        ]);
    }
}

if ($request->has('user_profile_document_ids')) {
    foreach ($request->user_profile_document_ids as $userProfileDocumentId) {
        \Yoelpc4\LaravelAttachment\Models\Attachment::find($userProfileDocumentId)->update([
            'attachable_id' => $user->id
        ]);
    }
}
```

The attachment identifications always an array with `{declared_name_in_dropzone}_ids` as hidden input's name.

## License

The Laravel attachment is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

[ico-version]: https://img.shields.io/packagist/v/yoelpc4/laravel-attachment.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/yoelpc4/laravel-attachment.svg?style=flat-square
[ico-license]: https://img.shields.io/packagist/l/yoelpc4/laravel-attachment.svg?style=flat-square
[ico-code-of-conduct]: https://img.shields.io/badge/Contributor%20Covenant-v2.0%20adopted-ff69b4.svg

[link-packagist]: https://packagist.org/packages/yoelpc4/laravel-attachment
