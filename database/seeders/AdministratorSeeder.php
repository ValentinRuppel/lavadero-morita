<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administrator;
use App\Models\User; // Para asociar a un cliente si es necesario
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea un administrador SIN perfil de cliente asociado
        Administrator::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_id' => null, // No asociado a un cliente
            ]
        );

        // Crea un administrador que TAMBIÉN es un cliente
        $clientAdminUser = User::firstOrCreate(
            ['email' => 'client_admin@example.com'],
            [
                'name' => 'Client Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Administrator::firstOrCreate(
            ['email' => 'admin_with_client@example.com'],
            [
                'name' => 'Admin with Client Profile',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_id' => $clientAdminUser->id, // Asociado al cliente creado
            ]
        );
    }
}