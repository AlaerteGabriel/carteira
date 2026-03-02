<?php
namespace App\Services;

use App\Models\Transacoes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class TransacoesService
{
    /**
     * Processa a transferência entre dois usuários.
     */
    public function transferir(User $pagador, User $beneficiario, float $valor): Transacoes
    {

        // 1. Validação de saldo (usando o atributo que criamos no Model)
        // Pega o saldo calculado pelo Model
        $currentBalance = $pagador->balanco;

        // Se o saldo for menor que o valor que ele quer enviar, bloqueia.
        // Isso cobre: Saldo insuficiente E saldo negativo.
        if ($currentBalance < $valor) {
            $diff = $valor - $currentBalance;
            throw new Exception("Saldo insuficiente. Você possui R$ {$currentBalance}, faltam R$ {$diff} para esta operação.");
        }

        // 2. Transação de Banco de Dados
        return DB::transaction(function () use ($pagador, $beneficiario, $valor) {

            // Registra a saída para o Payer (quem envia)
            return Transacoes::create([
                'tr_us_id'           => $pagador->us_id,
                'tr_pagador_id'      => $pagador->us_id,
                'tr_beneficiario_id' => $beneficiario->us_id,
                'tr_valor'           => $valor,
                'tr_tipo'            => 'transferencia',
                'tr_descricao'       => "Envio de valor para {$beneficiario->us_nome}",
            ]);

            // Nota: Em um sistema real, você também poderia registrar a entrada
            // explicitamente para o Beneficiario se quiser extratos separados por usuário.
        });
    }

    /**
     * Processa um depósito simples.
     */
    public function deposito(User $user, float $valor): Transacoes
    {
        return Transacoes::create([
            'tr_us_id'    => $user->us_id,
            'tr_valor'    => $valor,
            'tr_tipo'     => 'deposito',
            'tr_descricao' => "Depósito em conta",
        ]);
    }

    public function estornar(Transacoes $originalTransaction): Transacoes
    {
        if ($originalTransaction->tr_estornado) {
            throw new \Exception("Esta transação já foi estornada.");
        }

        return DB::transaction(function () use ($originalTransaction) {
            // 1. Marca a original como estornada
            $originalTransaction->update(['tr_estornado' => true]);

            // 2. Cria a transação de estorno (Invertendo Payer e Payee)
            $reversal = Transacoes::create([
                'tr_us_id'              => auth()->id(),
                'tr_pagador_id'         => $originalTransaction->tr_beneficiario_id, // Quem recebeu agora devolve
                'tr_beneficiario_id'    => $originalTransaction->tr_pagador_id, // Quem enviou agora recebe de volta
                'tr_valor'              => $originalTransaction->tr_valor,
                'tr_tipo'               => 'transferencia',
                'tr_descricao'          => "Estorno de transação #{$originalTransaction->tr_id}",
                'tr_estorno_id'         => $originalTransaction->tr_id
            ]);

            return $reversal;
        });
    }
}
