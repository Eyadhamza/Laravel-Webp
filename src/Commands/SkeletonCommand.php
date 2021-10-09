<?php

namespace Pi\LaravelWebp\Commands;

use Illuminate\Console\Command;

class LaravelWebpCommand extends Command
{
    public $signature = 'laravel-webp';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
