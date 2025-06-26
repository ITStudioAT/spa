<?php

namespace Itstudioat\Spa\Commands;

use Illuminate\Console\Command;

class SpaPackages extends Command
{
    protected $signature = 'spa:packages';
    protected $description = 'Installiere JS-Abhängigkeiten für spa';

    public function handle()
    {
        $deps = [
            '@vitejs/plugin-vue',
            'pinia',
            'sass-embedded',
            'vite-plugin-vuetify',
            'vue',
            'vue-router',
            'vuetify',
        ];

        $devDeps = [
            'axios',
            'laravel-vite-plugin',
            'vite',
        ];

        foreach ($deps as $pkg) {
            $this->info("📦 Installing latest {$pkg} ...");
            exec("npm install {$pkg}@latest");
        }

        foreach ($devDeps as $pkg) {
            $this->info("📦 Installing latest dev {$pkg} ...");
            exec("npm install --save-dev {$pkg}@latest");
        }

        $this->info('✅ Alle Pakete installiert.');
    }
}
