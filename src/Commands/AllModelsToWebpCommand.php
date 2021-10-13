<?php

namespace EyadHamza\LaravelWebp\Commands;

use Illuminate\Console\Command;

class AllModelsToWebpCommand extends Command
{
    protected $signature = 'models:to-webp {folder?}';

    protected $description = 'convert images in all the existing models that
     has the imageFields to webp images';

    public function handle()
    {
        $folder = $this->argument('folder') ?? null;
        foreach (getModels($folder) as $model) {
            if (method_exists($model, 'getImagesField')) {
                $model->all()->each(function ($object) {
                    foreach (collect($object->getImagesField()) as $key => $fieldValue) {
                        $object->convertImageInDatabase($key);
                    }
                });
            }
        }
    }
}
