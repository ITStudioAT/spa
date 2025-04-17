<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    // The name and signature of the console command.
    protected $signature = 'user:create {first_name} {last_name} {email} {password}';

    // The console command description.
    protected $description = 'Create a new user with first_name, last_name, email, and password';

    // Execute the console command.
    public function handle()
    {
        // Get the input data
        $first_name = $this->argument('first_name');
        $last_name = $this->argument('last_name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Validate if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error('Email already exists!');
            return;
        }

        // Create the new user
        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($password), // Hash the password
        ]);

        // Output success message
        $this->info("User {$user->first_name} {$user->last_name} created successfully.");
    }
}
