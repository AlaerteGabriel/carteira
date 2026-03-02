<?php

namespace App\Policies;

use App\Models\Transacoes;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransacoesPolicy
{

    public function estornar(User $user, Transacoes $transaction): bool
    {
        // 1. O usuário logado deve ser o dono do registro (quem enviou ou depositou)
        // E a transação não pode já ter sido estornada
        // E a transação não pode ser ela mesma um estorno

        $isOwner = (int) $user->us_id === (int) $transaction->tr_us_id;
        $notReversed = !$transaction->tr_estornado;
        $notAReversal = is_null($transaction->tr_estorno_id);

        return $isOwner && $notReversed && $notAReversal;
    }

}
