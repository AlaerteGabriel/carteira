<?php

namespace App\Http\Controllers;

use App\Models\Transacoes;
use App\Models\User;
use App\Services\TransacoesService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class TransacoesController extends Controller
{

    public function storeTransfer(Request $request, TransacoesService $service)
    {

        $valid = $request->validate([
            'beneficiario' => 'required|email|exists:fi_users,us_email',
            'valor' => 'required|numeric|min:0.01'
        ]);

        $pagador = auth()->user(); // Usuário logado
        $beneficiario = User::where('us_email', $request->beneficiario)->first();

        // 3. Validações de regra de negócio antes de processar
        if (!$beneficiario) {
            return back()->withErrors(['email_pix' => 'Chave PIX (e-mail) não encontrada.']);
        }

        if ($beneficiario->us_id === auth()->id()) {
            return back()->withErrors(['email_pix' => 'Você não pode transferir para si mesmo.']);
        }

        try {
            $service->transferir($pagador, $beneficiario, $valid['valor']);
            return back()->withInput()->with('success', 'Transferência realizada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

    }

    public function storeDeposit(Request $request, TransacoesService $service)
    {

        $valid = $request->validate([
            'valor' => 'required|numeric|min:0.01'
        ]);

        $user = auth()->user(); // Usuário logado

        try {
            $service->deposito($user, $valid['valor']);
            return back()->withInput()->with('success', 'Depósito realizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

    }

    /**
     * @throws AuthorizationException
     */
    public function estornar(Transacoes $transaction, TransacoesService $service)
    {
        // 1. Valida pela Policy se o usuário logado pode estornar esta transação
        $this->authorize('estornar', $transaction);

        try {
            $service->estornar($transaction);
            return back()->with('success', 'Transação estornada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
