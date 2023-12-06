<?php

namespace EyadHamza\LaravelWebp\Commands;

use EyadHamza\LaravelWebp\Casts\ToWebpCast;
use EyadHamza\LaravelWebp\Services\WebpService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ToWebpImageFieldCommand extends Command
{
    protected $signature = 'images:to-webp {model}';

    protected $description = 'convert all images in the database to webp images';

    public function handle(): void
    {
        $model = $this->argument('model');

        /* @var Model $model */
        $model = class_exists($model) ? app($model) : app("App\Models\\" . $model);

        $imageAttributeKeys = collect($model->getCasts())
            ->filter(fn ($cast, $attribute) => Str::startsWith($cast, ToWebpCast::class))
            ->keys();

        $toBeUpserted = $model::query()->chunkMap(function (Model $object) use ($imageAttributeKeys) {
            $imageAttributes = $imageAttributeKeys
                ->filter(fn ($attribute) => $object->getAttribute($attribute))
                ->mapWithKeys(function ($attribute) use ($object) {
                    $settingsDto = ToWebpCast::convertCastToDto($object->getCasts()[$attribute]);

                    return [$attribute => WebpService::make($object->getAttribute($attribute), $settingsDto)->save()];
                })
                ->all();

            return array_merge($object->getAttributes(), $imageAttributes);
        }, 100)->all();

        $model::query()->upsert($toBeUpserted, ['id'], $imageAttributeKeys->all());
    }
}
