<?php

namespace Database\Seeders;

use App\Models\Transacoes;
use App\Models\User;
use Database\Factories\TransacoesFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $service = app(\App\Services\TransacoesService::class);

        // 1. Criar usuário principal
        $admin = User::factory()->create([
            'us_nome' => 'Alaerte Gabriel',
            'us_email' => 'contato@alaertegabriel.com.br',
        ]);

        $service->deposito($admin, $admin->us_balanco);

        // 1. Criar usuários para teste
        User::factory(10)->create()->each(function ($user) use ($service) {
            // Cada um começa com um valor aleatório, mas registrado oficialmente
            $service->deposito($user, $user->us_balanco);
        });
    }
}
