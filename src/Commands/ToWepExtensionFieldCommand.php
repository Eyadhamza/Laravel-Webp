<?php

namespace EyadHamza\LaravelWebp\Commands;

use Illuminate\Console\Command;

class ToWepExtensionFieldCommand extends Command
{
    protected $signature = 'change:extension-webp {model}';

    protected $description = 'check for a column named
    |mime and extension| and apply webp changes';

    public function handle()
    {
        $model = $this->argument('model');
        $model = app("App\Models\\".$model);
        $model::all()->each(function ($object) {
            $object->fill([
                'mime' => 'image\webp',
                'extension' => 'webp',
            ]);
            $object->save();
        });
    }
}
