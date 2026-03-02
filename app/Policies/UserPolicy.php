<?php

namespace App\Policies;

use App\Models\Transacoes;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function ePai(Transacoes $op, User $user) : bool
    {
        // 1. O administrador sempre pode atualizar
        if($op->isRootOrAdmin()) {
            return true;
        }

        // 2. Verifica se o usuário é o dono direto do cliente
        if($user->member_id === $op->id) {
            return true;
        }

        // 3. Checa a hierarquia de revenda
        $currentOwner = $user->owner; // Supondo que você tenha uma relação "owner" no modelo Client
        while ($currentOwner) {
            if ($currentOwner->id === $op->id) {
                return true;
            }
            // Sobe um nível na hierarquia
            $currentOwner = $currentOwner->owner; // Supondo que a relação de revenda seja "owner"
        }

        // 4. Se nenhuma das condições acima for atendida, nega a permissão
        return false;
    }

}
