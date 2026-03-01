<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating a new admin user...');

        $name = $this->ask('Full Name');
        $email = $this->ask('Email Address');
        $password = $this->secret('Password');
        $passwordConfirm = $this->secret('Confirm Password');

        // Validation
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirm,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $this->error('Admin user creation failed due to validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        try {
            // Attempt to create the user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                // Assuming is_admin column exists, but usually Laravel auth just uses standard email/password
            ]);

            // If there's an is_admin or role column, try to set it dynamically if it exists
            if (\Schema::hasColumn('users', 'is_admin')) {
                $user->is_admin = true;
                $user->save();
            }

            $this->info("Admin user [{$email}] created successfully! You can now login.");
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}
