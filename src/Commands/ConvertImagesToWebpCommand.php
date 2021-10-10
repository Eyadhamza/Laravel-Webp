<?php

namespace Pi\LaravelWebp\Commands;

use Illuminate\Console\Command;
use Pi\LaravelWebp\Services\ImageToWebpService;

class ConvertImagesToWebpCommand extends Command
{
    protected $signature = 'images:convert-webp {model} {attribute}';

    protected $description = 'convert all images in the database to webp';

    public function handle()
    {
        $model = $this->argument('model');
        $attribute = $this->argument('attribute');
        $model = app("App\Models\\".$model);

        $model->all()->each(function ($object) use ($attribute) {
            $object->fill([
                $attribute => (new ImageToWebpService())->getWebpExtension($object[data_get($object, 'imageField')]),
            ]);
            $object->save();
        });
    }
}
