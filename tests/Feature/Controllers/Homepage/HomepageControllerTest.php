<?php

use Itstudioat\Spa\Models\User;
use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use Itstudioat\Spa\Services\AdminService;
use Illuminate\Support\Facades\Notification;


it('calls index: /', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('<div id="app">', false); // prüft, ob Vue-Mount-Point da ist
});


it('calls config: /api/homepage/config and retuns as json', function () {
    config([
        'spa.logo' => 'logo.png',
        'spa.copyright' => '© 2025',
        'spa.timeout' => 5000,
        'spa.title' => 'Meine App',
        'spa.company' => 'CoolCompany',
    ]);

    $response = $this->getJson('/api/homepage/config'); // oder die Route, die du benutzt

    $response->assertStatus(200);
    $response->assertExactJson([
        'logo' => 'logo.png',
        'copyright' => '© 2025',
        'timeout' => 5000,
        'title' => 'Meine App',
        'company' => 'CoolCompany',
    ]);
});

it('registers a new user and returns REGISTER_ENTER_TOKEN', function () {
    Notification::fake(); // Verhindert echtes Senden von Mails

    // Request-Payload (wie vom Frontend)
    $payload = [
        'data' => [
            'email' => 'test@example.com',
            'step' => 'REGISTER_START', // oder leer – wichtig für AdminService-Logik
        ],
    ];

    // Dummy-User zum Zurückgeben
    $user = User::factory()->make([
        'email' => $payload['data']['email'],
    ]);

    // AdminService mocken
    $this->mock(AdminService::class, function ($mock) use ($user, $payload) {
        // User existiert nicht → create
        $mock->shouldReceive('checkRegister')
            ->once()
            ->with($payload['data'])
            ->andReturn(null);

        // User wird erstellt
        $mock->shouldReceive('createRegisterUser')
            ->once()
            ->with($payload['data'])
            ->andReturn($user);

        // Token wird verschickt
        $mock->shouldReceive('sendRegisterToken')
            ->once()
            ->with(1, $user, $payload['data']['email']);
    });

    // API-Aufruf
    $response = $this->postJson('/api/admin/register/register_step_1', $payload);

    // Teste Response
    $response->assertStatus(200);
    $response->assertExactJson([
        'step' => 'REGISTER_ENTER_TOKEN',
    ]);
});
