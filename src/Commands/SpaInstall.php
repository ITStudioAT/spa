<?php


namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;

class SpaInstall extends Command
{
    protected $signature = 'spa:install';
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

        $json['scripts']['docs:dev'] = 'vuepress dev docs';
        $json['scripts']['docs:build'] = 'vuepress build docs';

        $json['dependencies']['@vitejs/plugin-vue'] = '^5.0.4';
        $json['dependencies']['filepond'] = '^4.31.1';
        $json['dependencies']['filepond-plugin-file-validate-type'] = '^1.2.9';
        $json['dependencies']['filepond-plugin-image-preview'] = '^4.6.12';
        $json['dependencies']['pinia'] = '^2.1.7';
        $json['dependencies']['puppeteer'] = '^23.4.0';
        $json['dependencies']['sass-embedded'] = '^1.83.4';
        $json['dependencies']['shiki'] = '^1.20.0';
        $json['dependencies']['vite-plugin-vuetify'] = '^2.0.3';
        $json['dependencies']['vue'] = '^3.4.21';
        $json['dependencies']['vue-filepond'] = '^7.0.4';
        $json['dependencies']['vue-router'] = '^4.3.0';
        $json['dependencies']['vuetify'] = '^3.5.11';


        $json['devDependencies']['@vuepress/bundler-vite'] = '^2.0.0-rc.19';
        $json['devDependencies']['@vuepress/plugin-markdown-image'] = '^2.0.0-rc.86';
        $json['devDependencies']['@vuepress/theme-default'] = '^2.0.0-rc.74';
        $json['devDependencies']['axios'] = '^1.6.4';
        $json['devDependencies']['laravel-vite-plugin'] = '^1.0';
        $json['devDependencies']['vite'] = '^5.0';
        $json['devDependencies']['vuepress'] = '^2.0.0-rc.19';


        ksort($json['dependencies']);
        ksort($json['devDependencies']);

        file_put_contents($packageJsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('Vue dependencies added to package.json. Run npm install to install them.');
    }
}
