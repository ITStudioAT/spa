<?php

namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;

class SpaCommand extends Command
{
    public $signature = 'spa';

    public $description = 'My command';

    public function handle(): int
    {
        $text = config('spa.version');
        $this->comment($text);

        return 0;
    }
}
