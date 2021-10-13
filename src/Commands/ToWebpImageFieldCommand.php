<?php

namespace EyadHamza\LaravelWebp\Commands;

use EyadHamza\LaravelWebp\ImageToWebp;
use Illuminate\Console\Command;

class ToWebpImageFieldCommand extends Command
{
    protected $signature = 'images:to-webp {model} {attribute?}';

    protected $description = 'convert all images in the database to webp images';

    public function handle()
    {
        $model = $this->argument('model');
        $model = class_exists($model) ? app($model) : app("App\Models\\" . $model);
        $attribute = $this->argument('attribute');
        $model->all()->each(function ($object) use ($attribute) {
            if ($attribute) {
                $object->fill([$attribute => ImageToWebp::make($object->$attribute)->getWebpFullPath()]);
                $object->save();
            } else {
                foreach (collect($object->getImagesField()) as $key => $fieldValue) {

                    $object->convertImageInDatabase($key, ImageToWebp::make($fieldValue)->getWebpFullPath());
                }
            }
        });
    }
}
