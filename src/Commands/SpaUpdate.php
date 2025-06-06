<?php

namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Spa\InstallUpdateController;

class SpaUpdate extends Command
{
    // Kein Argument in der Signature definieren
    protected $signature = 'spa:update';

    protected $description = 'Führt ein Update der Applikation durch';

    public function handle()
    {
        $controller = app(InstallUpdateController::class);

        $request = Request::create('', 'GET');
        $response = $controller->index($request);

        $this->info('✅ Das Update wurde erfolreich ausgeführt!');

        return Command::SUCCESS;
    }
}
