<?php

use Illuminate\Console\Command;
use function Pest\Laravel\artisan;

it('can test', function () {
    artisan(\ItStudioat\Spa\Commands\SpaCommand::class)->assertExitCode(Command::SUCCESS);
});
