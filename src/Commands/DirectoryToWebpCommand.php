<?php

namespace EyadHamza\LaravelWebp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use EyadHamza\LaravelWebp\Services\ImageToWebpService;

class DirectoryToWebpCommand extends Command
{
    protected $signature = 'public:to-webp
     {directory=public}
     {--overwrite : Whether the images should be deleted after conversion}
     {--assets}';

    protected $description = 'Optimize images in public directory';

    private $imageService;

    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageToWebpService();
    }

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
                $this->imageService->setPath($file);

                $shouldOverwrite ?
                    $this->imageService->overwrite() :
                    $this->imageService->save();

                $this->info($this->imageService->printInfo());
            } catch (\Exception $e) {
                $this->info($e->getMessage());

                continue;
            }
        }
    }
}
