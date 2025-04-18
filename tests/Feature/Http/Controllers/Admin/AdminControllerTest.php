<?php



//=== public function config()
it('returns config values', function () {

    config()->set('spa.logo', 'test.png');
    config()->set('spa.title', 'Test Title');

    $response = $this->get('api/admin/config');

    $response->assertStatus(200);

    $response->assertJsonFragment([
        'logo' => 'test.png',
        'title' => 'Test Title',
        'timeout' => config('spa.timeout'),
        'title' => config('spa.title'),
        'company' => config('spa.company'),
    ]);
});
