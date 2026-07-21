<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mishop.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        $this->command->info('Utilisateur admin créé avec succès!');
        $this->command->info('Email: admin@mishop.com');
        $this->command->info('Mot de passe: admin123');
    }
}
