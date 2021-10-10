<?php

namespace Pi\LaravelWebp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Pi\LaravelWebp\Services\ImageToWebpService;

class AssetsToWebpCommand extends Command
{
    protected $signature = 'asset:to-webp';

    protected $description = 'convert images in all assets to webp';
    /**
     * @var ImageToWebpService
     */
    private $imageService;

    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageToWebpService();
    }

    public function handle()
    {
        foreach (File::allFiles('public') as $file) {
            try {
                $this->imageService->setPath($file->getRealPath());

                $this->imageService->overwrite();

                $this->info($this->imageService->printInfo());
            } catch (\Exception $e) {
                $this->info($e->getMessage());

                continue;
            }
        }
    }
}
