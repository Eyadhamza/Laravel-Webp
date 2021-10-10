<?php

namespace Pi\LaravelWebp\Commands;

use Illuminate\Console\Command;
use Pi\LaravelWebp\ImageToWebp;
use Pi\LaravelWebp\Services\ImageToWebpService;

class ToWebpImageFieldCommand extends Command
{
    protected $signature = 'images:to-webp {model} {attribute?}';

    protected $description = 'convert all images in the database to webp images';

    public function handle()
    {
        $model = $this->argument('model');
        $attribute = $this->argument('attribute') ?? 'imageField';

        $model = class_exists($model) ? app($model) : app("App\Models\\".$model);

        $model->all()->each(function ($object) use ($attribute) {
            $object->fill([
                $attribute => ImageToWebp::getWebpFullPath($object[data_get($object, $attribute)]) ,
            ]);
            $object->save();
        });
    }
}
