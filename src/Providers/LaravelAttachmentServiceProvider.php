<?php

namespace Yoelpc4\LaravelAttachment\Providers;

use Illuminate\Support\ServiceProvider;
use Yoelpc4\LaravelAttachment\AttachmentManager;
use Yoelpc4\LaravelAttachment\Console\Commands\CreateFileAttachmentCommand;
use Yoelpc4\LaravelAttachment\Contracts\AttachmentRepository as AttachmentRepositoryContract;
use Yoelpc4\LaravelAttachment\Repositories\AttachmentRepository;

class LaravelAttachmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // merge package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/attachment.php', 'attachment');

        // binding attachment repository contract with attachment repository  concrete
        $this->app->bind(AttachmentRepositoryContract::class, AttachmentRepository::class);

        // binding attachment manager abstract with access manager concrete
        $this->app->bind('laravel_attachment.attachment_manager', function () {
            return new AttachmentManager;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // publish package configuration
        $this->publishes([
            __DIR__.'/../config/attachment.php' => config_path('attachment.php')
        ], 'config');

        // load package database migration
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2014_10_11_150613_create_attachments_table.php');

        // load package routes if activated in configuration
        if (config('attachment.routes.web.active')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if (config('attachment.routes.api.active')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        // load package views resources
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-attachment');

        // publish package views
        $this->publishes([
            __DIR__.'/../resources/sass'  => resource_path('sass/vendor/laravel-attachment'),
            __DIR__.'/../resources/js'    => resource_path('js/vendor/laravel-attachment'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-attachment'),
            __DIR__.'/../resources/icons' => storage_path('app/public/images/icons')
        ], 'views');

        // load package translations resources
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'laravel-attachment');

        // publish package translations
        $this->publishes([
            __DIR__.'/../resources/lang'  => resource_path('lang/vendor/laravel-attachment')
        ], 'lang');

        // register create attachment command when running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateFileAttachmentCommand::class
            ]);
        }

        // load the file attachment map
        \AttachmentManager::loadFileAttachmentMap();
    }
}
