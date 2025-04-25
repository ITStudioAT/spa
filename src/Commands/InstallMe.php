<?php


namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;

class InstallMe extends Command
{
    protected $signature = 'install:me';
    protected $description = 'Installiere JS-Abhängigkeiten für spa';

    public function handle()
    {

        // package.json
        $packageJsonPath = base_path('package.json');

        if (!file_exists($packageJsonPath)) {
            $this->error('package.json not found.');
            return;
        }

        $json = json_decode(file_get_contents($packageJsonPath), true);

        $json['dependencies']['@vitejs/plugin-vue'] = '^5.0.4';
        $json['dependencies']['pinia'] = '^2.1.7';
        $json['dependencies']['sass-embedded'] = '^1.83.4';
        $json['dependencies']['vite-plugin-vuetify'] = '^2.0.3';
        $json['dependencies']['vue'] = '^3.4.21';
        $json['dependencies']['vue-router'] = '^4.3.0';
        $json['dependencies']['vuetify'] = '^3.5.11';

        $json['devDependencies']['axios'] = '^1.6.4';
        $json['devDependencies']['laravel-vite-plugin'] = '^1.0';
        $json['devDependencies']['vite'] = '^5.0';


        ksort($json['dependencies']);
        ksort($json['devDependencies']);

        file_put_contents($packageJsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('✅ Vue dependencies added to package.json. Run npm install to install them.');
    }
}
