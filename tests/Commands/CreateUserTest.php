<?php

namespace Tests\Feature;

use App\Models\User;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Hash;
use Database\Factories\SpaUserFactory;
use Itstudioat\Spa\Commands\CreateUser;

class CreateUserCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Itstudioat\Spa\SpaServiceProvider::class,
        ];
    }

    public function test_it_creates_a_user_successfully()
    {
        User::where('email', 'max@mustermann.at')->delete();

        $this->artisan(CreateUser::class)
            ->expectsQuestion('Vorname', 'Max')
            ->expectsQuestion('Nachname', 'Mustermann')
            ->expectsQuestion('E-Mail', 'max@mustermann.at')
            ->expectsQuestion('Passwort (wird ausgeblendet)', 'secret123')
            ->expectsOutput('Benutzer Max Mustermann erfolgreich erstellt.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'max@mustermann.at',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
        ]);

        $user = User::where('email', 'max@mustermann.at')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_it_fails_if_email_already_exists()
    {

        User::where('email', 'max@mustermann.at')->delete();
        SpaUserFactory::new()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'email' => 'max@mustermann.at'
        ]);

        $this->artisan(CreateUser::class)
            ->expectsQuestion('Vorname', 'Max')
            ->expectsQuestion('Nachname', 'Mustermann')
            ->expectsQuestion('E-Mail', 'max@mustermann.at')
            ->expectsQuestion('Passwort (wird ausgeblendet)', 'secret123')
            ->expectsOutput('Diese E-Mail-Adresse existiert bereits!')
            ->assertExitCode(0);
    }
}
