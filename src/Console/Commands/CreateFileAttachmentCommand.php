<?php

namespace Yoelpc4\LaravelAttachment\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class CreateFileAttachmentCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:file-attachment {name : The name of the class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new file attachment class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'FileAttachment';

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * CreateFileAttachmentCommand constructor.
     *
     * @param  Filesystem  $files
     * @param  Composer  $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws FileNotFoundException
     */
    public function handle()
    {
        try {
            $handle = parent::handle();
        } catch (FileNotFoundException $e) {
            throw $e;
        }

        $this->composer->dumpAutoloads();

        return $handle;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Stubs/make-file-attachment.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\FileAttachments';
    }
}
