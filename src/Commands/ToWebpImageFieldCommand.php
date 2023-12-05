<?php

namespace EyadHamza\LaravelWebp\Commands;

use EyadHamza\LaravelWebp\Services\WebpService;
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
                $object->fill([$attribute => WebpService::make($object->$attribute)->getWebpRelativePath()]);
                $object->save();
            } else {
                foreach (collect($object->getImagesField()) as $key => $fieldValue) {
                    $object->convertImageInDatabase($key, WebpService::make($fieldValue)->getWebpRelativePath());
                }
            }
        });
    }
}
