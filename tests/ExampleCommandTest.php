<?php


use Illuminate\Console\Command;
use function Pest\Laravel\artisan;
use Itstudioat\Spa\Commands\SpaCommand;

it('can output the configure file', function () {
    artisan(SpaCommand::class)->expectsOutput(config('spa.version'))->assertExitCode(0);
});
