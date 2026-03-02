<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Transacoes;
use App\Services\TransacoesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransacaoFinanceiraTest extends TestCase
{
    use RefreshDatabase; // Garante que o banco seja resetado a cada teste

    /** @test */
    public function um_usuario_pode_transferir_dinheiro_para_outro_via_pix()
    {
        $pagador = User::factory()->create(); // Saldo 0
        $beneficiario = User::factory()->create();

        // Damos saldo real via depósito (isso gera a transação que o Observer lerá)
        $service = app(\App\Services\TransacoesService::class);
        $service->deposito($pagador, 1000.00);

        $this->actingAs($pagador->fresh()); // fresh() para carregar o saldo atualizado pelo observer

        $resposta = $this->post(route('carteira.transferir.store'), [
            'beneficiario' => $beneficiario->us_email,
            'valor'    => 350.00,
        ]);

        $resposta->assertRedirect();
        $resposta->assertSessionHas('success');

        $this->assertEquals(650.00, $pagador->fresh()->us_balanco);
        $this->assertEquals(350.00, $beneficiario->fresh()->us_balanco);
    }

    /** @test */
    public function um_usuario_nao_pode_transferir_mais_do_que_possui_em_saldo()
    {
        // Criamos um usuário com apenas R$ 50,00
        $pagador = User::factory()->create(['us_balanco' => 50.00]);
        $beneficiario = User::factory()->create(['us_email' => 'recebedor@teste.com']);

        $this->actingAs($pagador);

        // Tentamos transferir R$ 100,00
        $resposta = $this->post(route('carteira.transferir.store'), [
            'beneficiario' => 'recebedor@teste.com',
            'valor'    => 100.00,
        ]);

        // Verifica se retornou erros de validação
        $resposta->assertSessionHasErrors(['error']);

        // Garante que o saldo do pagador continua sendo R$ 50,00
        $this->assertEquals(50.00, $pagador->fresh()->us_balanco);
    }

    /** @test */
    public function um_deposito_deve_abater_saldo_negativo()
    {
        $usuario = User::factory()->create();
        $service = app(\App\Services\TransacoesService::class);

        // Simulamos uma situação onde ele recebeu 100 e gastou 200 (Saldo -100)
        // Para fins de teste, você pode criar uma transação de débito direto
        Transacoes::factory()->create([
            'tr_us_id' => $usuario->us_id,
            'tr_pagador_id' => $usuario->us_id,
            'tr_valor' => 100.00,
            'tr_tipo' => 'transferencia'
        ]);

        $service->deposito($usuario, 250.00);

        // O Observer vai somar: +250 (depósito) - 100 (transferência antiga) = 150
        $this->assertEquals(150.00, $usuario->fresh()->us_balanco);
    }

    /** @test */
    public function um_usuario_pode_estornar_sua_propria_transferencia_e_os_saldos_voltam_ao_original()
    {
        // 1. Preparação
        $pagador = User::factory()->create(['us_nome' => 'Alaerte Pagador']);
        $beneficiario = User::factory()->create(['us_nome' => 'Eduardo Beneficiario']);
        $service = app(TransacoesService::class);

        // Dá saldo inicial ao pagador via depósito (ativa o Observer)
        $service->deposito($pagador, 1000.00);

        // Realiza uma transferência de 400,00
        $transacaoOriginal = $service->transferir($pagador, $beneficiario, 400.00);

        // Verificação intermediária: Pagador deve ter 600 e Beneficiário 400
        $this->assertEquals(600.00, $pagador->fresh()->us_balanco);
        $this->assertEquals(400.00, $beneficiario->fresh()->us_balanco);

        // 2. Ação
        $this->actingAs($pagador);

        // Chama a rota de estorno que criamos
        $resposta = $this->post(route('carteira.transacoes.estorno', $transacaoOriginal->tr_id));

        // 3. Asserts de Fluxo
        $resposta->assertRedirect();
        $resposta->assertSessionHas('success');

        // 4. Asserts de Banco de Dados
        // Verifica se a original foi marcada como estornada
        $this->assertDatabaseHas('fi_transacoes', [
            'tr_id' => $transacaoOriginal->tr_id,
            'tr_estornado' => true
        ]);

        // Verifica se o registro de estorno foi criado invertendo os IDs
        $this->assertDatabaseHas('fi_transacoes', [
            'tr_pagador_id' => $beneficiario->us_id,    // Quem recebeu agora paga
            'tr_beneficiario_id' => $pagador->us_id,   // Quem pagou agora recebe
            'tr_valor' => 400.00,
            'tr_estorno_id' => $transacaoOriginal->tr_id
        ]);

        // 5. Asserts de Saldo Final (O momento da verdade para o Observer)
        $this->assertEquals(1000.00, $pagador->fresh()->us_balanco, 'O saldo do pagador não voltou para 1000.');
        $this->assertEquals(0.00, $beneficiario->fresh()->us_balanco, 'O saldo do beneficiário não voltou para 0.');
    }

    /** @test */
    public function um_usuario_nao_pode_estornar_uma_transacao_que_nao_lhe_pertence()
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();
        $service = app(TransacoesService::class);

        // Usuario B faz uma transação para um terceiro
        $terceiro = User::factory()->create();
        $service->deposito($usuarioB, 500.00);
        $transacaoB = $service->transferir($usuarioB, $terceiro, 100.00);

        // Usuario A tenta estornar a transação do B
        $this->actingAs($usuarioA);
        $resposta = $this->post(route('carteira.transacoes.estorno', $transacaoB->tr_id));

        // Deve retornar 403 por causa da Policy
        $resposta->assertStatus(403);
    }
}
