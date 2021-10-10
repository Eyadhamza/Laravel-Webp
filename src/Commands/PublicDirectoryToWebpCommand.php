<?php

namespace Pi\LaravelWebp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Pi\LaravelWebp\Services\ImageToWebpService;

class PublicDirectoryToWebpCommand extends Command
{
    protected $signature = 'public:to-webp';

    protected $description = 'Optimize images in public directory';

    private $imageService;

    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageToWebpService();
    }

    public function handle()
    {
        foreach (Storage::allFiles('public') as $file) {
            try {
                $this->imageService->setPath($file);

                $this->imageService->overwrite();

                $this->info($this->imageService->printInfo());
            } catch (\Exception $e) {
                $this->info($e->getMessage());

                continue;
            }
        }
    }
}
