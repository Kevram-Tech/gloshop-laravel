<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        $admin = User::where('email', 'admin@gloshop.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Admin GloShop',
                'email' => 'admin@gloshop.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@gloshop.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->warn('Admin user already exists!');
        }
    }
}

