<?php

it('can test /api/homepage/config', function () {

    $this->getJson('/api/homepage/config')
        ->assertOk()
        ->assertJson([
            'logo' => config('spa.logo', ''),
            'copyright' => config('spa.copyright', ''),
            'timeout' => config('spa.timeout', 3000),
            'title' => config('spa.title', 'Spa'),
            'company' => config('spa.company', 'ItStudio.at'),
        ]);
});
