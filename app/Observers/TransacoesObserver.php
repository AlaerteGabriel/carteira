<?php

namespace App\Observers;

use App\Models\Transacoes;
use App\Models\User;

class TransacoesObserver
{
    /**
     * Handle the Transacoes "created" event.
     */
    public function created(Transacoes $transacoes): void
    {
        // Coletamos todos os IDs envolvidos em um array e removemos duplicados e nulos
        $userIds = collect([
            $transacoes->tr_us_id,
            $transacoes->tr_pagador_id,
            $transacoes->tr_beneficiario_id
        ])->filter()->unique();

        // Disparamos a atualização apenas uma vez para cada usuário distinto
        $userIds->each(fn ($id) => $this->updateUserBalance($id));
    }

    /**
     * Recalcula e salva o saldo real do usuário na tabela fi_users.
     */
    private function updateUserBalance(int $userId): void
    {
        $user = \App\Models\User::find($userId);

//        if ($user) {
//            // Entradas: Depósitos (onde ele é o dono) + Transferências (onde ele é o beneficiário)
//            $incomes = Transacoes::where(function($q) use ($userId) {
//                $q->where('tr_us_id', $userId)->where('tr_tipo', 'deposito');
//            })->orWhere('tr_beneficiario_id', $userId)->sum('tr_valor');
//
//            // Saídas: Transferências (onde ele é o pagador)
//            $expenses = Transacoes::where('tr_pagador_id', $userId)->sum('tr_valor');
//
//            // O saldo real é a diferença absoluta de todas as movimentações
//            $user->us_balanco = (float) ($incomes - $expenses);
//            $user->save();
//        }

        if ($user) {
            // ENTRADAS:
            // 1. Depósitos que ele mesmo fez (us_id + type deposit)
            // 2. Transferências onde ele é o beneficiário (payee_id)
            $incomes = Transacoes::where(function($q) use ($userId) {
                $q->where('tr_us_id', $userId)->where('tr_tipo', 'deposito');
            })->orWhere('tr_beneficiario_id', $userId)->sum('tr_valor');

            // SAÍDAS:
            // 1. Transferências onde ele é o pagador (tr_pagador_id)
            // Nota: No estorno, o antigo beneficiário vira o pagador (payer_id)
            $expenses = Transacoes::where('tr_pagador_id', $userId)->sum('tr_valor');

            // O saldo final TEM que considerar os dois lados
            $user->us_balanco = (float) ($incomes - $expenses);
            $user->save();
        }
    }
}
