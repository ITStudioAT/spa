<?php

namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;

class SpaCommand extends Command
{
    public $signature = 'spa';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
