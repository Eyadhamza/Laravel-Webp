<?php

namespace EyadHamza\LaravelWebp\Commands;

use Exception;
use EyadHamza\LaravelWebp\Services\WebpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DirectoryToWebpCommand extends Command
{
    protected $signature = 'public:to-webp
     {directory=public}
     {--overwrite : Whether the images should be deleted after conversion}
     {--assets}';

    protected $description = 'Optimize images in public directory';

    public function handle()
    {
        $directory = $this->argument('directory');
        $shouldOverwrite = $this->option('overwrite');
        $isAssetDirectory = $this->option('assets');

        $directoryFiles = $isAssetDirectory ?
            File::allFiles($directory) :
            Storage::allFiles($directory);

        foreach ($directoryFiles as $file) {
            try {
                $service = WebpService::make($file);

                $shouldOverwrite ? $service->overwrite() : $service->save();

                $this->info($service->printInfo());
            } catch (Exception $e) {
                $this->info($e->getMessage());

                continue;
            }
        }
    }
}
